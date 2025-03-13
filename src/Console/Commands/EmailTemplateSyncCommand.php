<?php

namespace Crow\LaravelEmailTemplate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Crow\LaravelEmailTemplate\Models\EmailTemplate;

class EmailTemplateSyncCommand extends Command
{
    protected $signature = 'email:template:sync';
    protected $description = 'Synchronization of email templates';
    private ?string $dataClassName = null;
    private ?string $dataMethodName = null;
    private ?bool $syncCreate = false;
    private ?bool $syncDelete = false;
    private ?Collection $syncUpdateFields = null;

    public function handle()
    {
        $this->setupConfig();

        if (!$this->dataClassName || !$this->dataMethodName) {
            $this->error('Config sync not setup class name or class static method');
            return;
        }

        $data = $this->getData();

        $this->checkData($data);
    }

    private function setupConfig(): void
    {
        $this->dataClassName = Config::get('email_template.data.class');
        $this->dataMethodName = Config::get('email_template.data.method');
        $this->syncCreate = (bool)Config::get('email_template.sync.create');
        $this->syncDelete = (bool)Config::get('email_template.sync.delete');
        $this->syncUpdateFields = Collection::make(Config::get('email_template.sync.update_fields'));
    }

    private function getData(): array
    {
        return $this->dataClassName::{$this->dataMethodName}();
    }

    private function checkData(array $data): void
    {
        $data = $this->getDataFormatted($data);

        foreach (EmailTemplate::query()->cursor() as $row) {
            if (!$data->get($row->type)) {
                if ($this->syncDelete) {
                    if ($row->is_custom) {
                        continue;
                    }
                    $row->delete();
                }
                continue;
            }
            if ($data->get($row->type)) {
                $this->updateRow($row, Collection::make($data->get($row->type)));
                $data->offsetUnset($row->type);
            }
        }

        $this->createRows($data);
    }

    private function getDataFormatted(array $data): Collection
    {
        $collect = Collection::make();

        foreach ($data as $row) {
            $collect->put($row['type'], $row);
        }

        return $collect;
    }

    private function updateRow(EmailTemplate $emailTemplate, Collection $data): void
    {
        if ($emailTemplate->is_custom) {
            return;
        }

        foreach ($this->syncUpdateFields as $field) {
            $emailTemplate->$field = $data->get($field);
        }
        $emailTemplate->save();
    }

    private function createRows(Collection $data): void
    {
        if (!$this->syncCreate) {
            return;
        }

        foreach ($data as $row) {
            $row = Collection::make($row);
            $row->offsetUnset('id');
            $emailTemplate = new EmailTemplate();
            foreach ($row->keys() as $field) {
                $emailTemplate->$field = $row->get($field);
            }
            $emailTemplate->save();
        }
    }
}
