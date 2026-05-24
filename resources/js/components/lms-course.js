/**
 * @typedef {object} LmsCourse
 * @property {string} activeLesson
 * @property {string[]} lessonIds
 * @property {() => void} init
 * @property {() => number} activePosition
 * @property {() => void} previousLesson
 * @property {() => void} nextLesson
 * @property {() => boolean} isFirstLesson
 * @property {() => boolean} isLastLesson
 */

/**
 * @param {string[]} lessonIds
 * @returns {LmsCourse}
 */
export function lmsCourse(lessonIds = []) {
    assertStringArray(lessonIds, 'lessonIds');

    return {
        activeLesson: '',
        lessonIds,

        init() {
            this.activeLesson = this.lessonIds[0] || '';
        },

        activePosition() {
            return this.lessonIds.indexOf(this.activeLesson);
        },

        previousLesson() {
            const position = this.activePosition();

            if (position > 0) {
                this.activeLesson = this.lessonIds[position - 1];
            }
        },

        nextLesson() {
            const position = this.activePosition();

            if (position < this.lessonIds.length - 1) {
                this.activeLesson = this.lessonIds[position + 1];
            }
        },

        isFirstLesson() {
            return this.activePosition() <= 0;
        },

        isLastLesson() {
            return this.activePosition() === this.lessonIds.length - 1;
        },
    };
}

/**
 * @param {unknown} value
 * @param {string} name
 * @returns {asserts value is string[]}
 */
function assertStringArray(value, name) {
    if (!Array.isArray(value) || value.some(item => typeof item !== 'string')) {
        throw new TypeError(`${name} must be an array of strings.`);
    }
}
