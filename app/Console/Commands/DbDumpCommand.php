<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DbDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump {--path= : The path to save the dump file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps the PostgreSQL database to a file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = config('database.connections.pgsql');

        if (empty($config) || $config['driver'] !== 'pgsql') {
            $this->error('PostgreSQL connection not configured correctly.');

            return 1;
        }

        $host = $config['host'];
        $port = $config['port'];
        $dbName = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        $path = $this->option('path') ?: storage_path('app/db-dumps');
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $fileName = 'dump_'.now()->format('Y-m-d_H-i-s').'.sql';
        $filePath = $path.'/'.$fileName;

        $command = "pg_dump -h {$host} -p {$port} -U {$username} -d {$dbName}";

        $process = Process::fromShellCommandline($command);
        $process->setTty(false);
        $process->setTimeout(3600);
        $process->setEnv(['PGPASSWORD' => $password]);

        try {
            $process->mustRun();
            file_put_contents($filePath, $process->getOutput());
            $this->info("Database dumped successfully to: {$filePath}");
        } catch (ProcessFailedException $exception) {
            $this->error('The database dump failed.');
            $this->error($exception->getMessage());

            return 1;
        }

        return 0;
    }
}
