<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord; // この行を追加

class CustomFormatter extends LineFormatter {
    
    public function format(LogRecord $record): string {
        $originalMessage = $record->message;

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $caller = $this->findCaller($backtrace);

        $formattedMessage = "@@@ [{$caller['class']}::{$caller['function']}] " . $originalMessage;

        $record = $record->with(message: $formattedMessage);

        return parent::format($record);
    }

    private function findCaller(array $backtrace): array {
        foreach ($backtrace as $trace) {
            // MonologやLaravelの内部呼び出しをスキップ
            if (isset($trace['class']) && str_contains($trace['class'], 'Monolog') || str_contains($trace['class'], 'Illuminate')) {
                continue;
            }
            if (isset($trace['class'], $trace['function'])) {
                return [
                    'class' => $trace['class'],
                    'function' => $trace['function'],
                ];
            }
        }
        return ['class' => 'N/A', 'function' => 'N/A'];
    }
}