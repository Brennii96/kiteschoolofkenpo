<?php

declare(strict_types=1);

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Entry;
use Statamic\View\View;

final class TrainingHallController extends Controller
{
    public function __invoke(?string $grade = null): View|RedirectResponse
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();
        $grades = $this->gradeNavigation();
        $firstGrade = $grades->first();

        abort_if(! is_array($firstGrade), 404);

        if ($grade === null) {
            return redirect()->route('members.training-hall', ['grade' => $firstGrade['slug']]);
        }

        $activeGradeSummary = $grades->first(fn (array $entry): bool => $entry['slug'] === $grade);

        abort_if(! is_array($activeGradeSummary), 404);

        $hasIncompletePayment = $member->hasIncompletePayment();
        $canViewTraining = $member->hasVerifiedEmail()
            && $member->isApproved()
            && $member->subscribed();
        $activeGradeCacheKey = sprintf(
            'training_hall_grade_%s_%d_%d',
            $grade,
            (int) $canViewTraining,
            (int) $hasIncompletePayment,
        );
        $shouldLoadActiveGrade = $this->shouldLoadActiveGrade($activeGradeCacheKey);
        $activeGrade = $shouldLoadActiveGrade
            ? Entry::query()
                ->where('collection', 'grades')
                ->where('published', true)
                ->where('slug', $grade)
                ->first()
            : null;

        abort_if($shouldLoadActiveGrade && ! $activeGrade instanceof EntryContract, 404);

        $seniorGrades = $this->navigationGrades($grades, 'Senior', $grade);
        $juniorGrades = $this->navigationGrades($grades, 'Junior', $grade);

        return new View()
            ->template('members.training-hall.index')
            ->layout('layout')
            ->with([
                'member' => $member,
                'is_subscribed' => $member->subscribed(),
                'has_incomplete_payment' => $hasIncompletePayment,
                'active_grade' => $activeGrade,
                'active_grade_type' => $activeGradeSummary['grade_type'],
                'senior_grades' => $seniorGrades,
                'junior_grades' => $juniorGrades,
                'senior_url' => $seniorGrades->first()['url'] ?? null,
                'junior_url' => $juniorGrades->first()['url'] ?? null,
                'can_view_training' => $canViewTraining,
                'active_grade_cache_key' => $activeGradeCacheKey,
            ]);
    }

    private function gradeNavigation(): Collection
    {
        return Cache::remember('training-hall.grade-navigation', now()->addHours(3), function (): Collection {
            return Entry::query()
                ->where('collection', 'grades')
                ->where('published', true)
                ->orderBy('order')
                ->orderBy('title')
                ->get()
                ->map(fn (EntryContract $entry): array => [
                    'title' => $entry->value('title'),
                    'slug' => $entry->slug(),
                    'grade_type' => $entry->value('grade_type'),
                    'belt_image_url' => $this->assetUrl($entry),
                ])
                ->values();
        });
    }

    private function navigationGrades(Collection $grades, string $gradeType, string $activeGradeSlug): Collection
    {
        return $grades
            ->filter(fn (array $entry): bool => $entry['grade_type'] === $gradeType)
            ->map(fn (array $entry): array => [
                'title' => $entry['title'],
                'slug' => $entry['slug'],
                'grade_type' => $entry['grade_type'],
                'belt_image_url' => $entry['belt_image_url'],
                'url' => route('members.training-hall', ['grade' => $entry['slug']]),
                'is_active' => $entry['slug'] === $activeGradeSlug,
            ])
            ->values();
    }

    private function shouldLoadActiveGrade(string $cacheKey): bool
    {
        if (! config('statamic.system.cache_tags_enabled', true)) {
            return true;
        }

        return ! Cache::has($cacheKey);
    }

    private function assetUrl(EntryContract $entry): ?string
    {
        $asset = $entry->augmentedValue('belt_image')->value();

        if (is_object($asset) && method_exists($asset, 'url')) {
            return $asset->url();
        }

        return null;
    }
}
