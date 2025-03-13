<?php

namespace Crow\LaravelEmailTemplate\Console\Commands;

use Crow\LaravelEmailTemplate\Providers\EmailTemplateServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class EmailTemplatePublishCommand extends Command
{
    protected $signature = 'email:template:publish {--tag=* : Tag for published}';
    protected $description = 'Publish files for email template package';
    private array $files = [];
    private array $fileTags = [
        'config',
        'migration',
        'lang'
    ];

    public function handle()
    {
        $option = is_array($this->option('tag')) && !empty($this->option('tag')) ? $this->option('tag')[0] : '';

        $this->parsePublishedFiles();

        switch ($option) {
            case 'config':
                $this->copyConfig();
                break;
            case 'migration':
                $this->copyMigration();
                break;
            default:
                $this->error('Not selected tag');
                break;
        }
    }

    private function parsePublishedFiles(): void
    {
        $index = 0;
        foreach (EmailTemplateServiceProvider::pathsToPublish(EmailTemplateServiceProvider::class) as $k => $v) {
            $this->files[$this->fileTags[$index]] = [
                'from' => $k,
                'to' => $v,
            ];
            $index++;
        }
    }

    private function copyConfig(): void
    {
        $this->copyFiles($this->files['config']['from'], $this->files['config']['to']);
    }

    private function copyMigration(): void
    {
        $filename = sprintf(
            '%s_create_%s.php',
            now()->format('Y_m_d_His'),
            Config::get('email_template.table')
        );

        $this->copyFile(
            $this->files['migration']['from'] . DIRECTORY_SEPARATOR . 'create_email_templates.stub',
            $this->files['migration']['to'] . DIRECTORY_SEPARATOR . $filename,
            Config::get('email_template.table')
        );
    }

    private function copyFiles(string $from, string $to): void
    {
        if (!file_exists($to)) {
            mkdir($to, 0755, true);
        }
        $from = rtrim($from, '/') . '/';
        $to = rtrim($to, '/') . '/';
        foreach (scandir($from) as $file) {
            if (!is_file($from . $file)) {
                continue;
            }

            $path = strtr(
                $to . $file,
                [
                    base_path() => ''
                ]
            );

            if (file_exists($to . $file)) {
                $this->info(
                    sprintf(
                        'File "%s" skipped',
                        $path
                    )
                );
                continue;
            }

            copy(
                $from . $file,
                $to . $file
            );

            $content = file_get_contents($to . $file);
            $content = strtr($content, ['{{TABLE_NAME}}' => Config::get('email_template.table')]);
            file_put_contents($to . $file, $content);

            $this->info(
                sprintf(
                    'File "%s" copied',
                    $path
                )
            );
        }
    }

    private function copyFile(string $from, string $to, ?string $table = null): void
    {
        copy(
            $from,
            $to
        );

        $content = file_get_contents($to);
        $content = strtr($content, [
            '{{TABLE_NAME}}' => $table,
        ]);
        file_put_contents($to, $content);

        $path = strtr(
            $to,
            [
                base_path() => ''
            ]
        );

        $this->info(
            sprintf(
                'File "%s" copied',
                $path
            )
        );
    }
}
