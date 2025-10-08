module.exports = {
  apps: [
    {
      name: 'laravel-scheduler',
      script: '/usr/bin/php',
      args: '/root/PinjamRuang/artisan schedule:run',
      cwd: '/root/PinjamRuang',
      instances: 1,
      autorestart: false, // Ubah ke false karena scheduler dijalankan via cron
      watch: false,
      max_memory_restart: '1G',
      env: {
        NODE_ENV: 'production'
      },
      cron_restart: '0 * * * *',
      log_file: '/root/PinjamRuang/storage/logs/pm2.log',
      out_file: '/root/PinjamRuang/storage/logs/pm2-out.log',
      error_file: '/root/PinjamRuang/storage/logs/pm2-error.log'
    },
    {
      name: 'laravel-worker',
      script: '/usr/bin/php',
      args: '/root/PinjamRuang/artisan queue:work --sleep=3 --tries=3',
      cwd: '/root/PinjamRuang',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '1G',
      env: {
        NODE_ENV: 'production'
      },
      log_file: '/root/PinjamRuang/storage/logs/worker.log',
      out_file: '/root/PinjamRuang/storage/logs/worker-out.log',
      error_file: '/root/PinjamRuang/storage/logs/worker-error.log'
    }
  ]
};
