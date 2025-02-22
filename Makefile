ssh-tunnel:
	ssh -L 3307:127.0.0.1:3306 -N -f 138.199.170.179

start:
	php artisan serve
