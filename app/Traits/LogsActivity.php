<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;

trait LogsActivity
{
    use SpatieLogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('model')
            ->setDescriptionForEvent(fn(string $eventName) => "Este registro foi {$eventName}");
    }

    /**
     * Logar ações críticas de segurança
     */
    public static function logSecurityAction($description, $properties = [])
    {
        activity('security')
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->log($description);
    }

    /**
     * Logar tentativas de login
     */
    public static function logLoginAttempt($email, $success = false, $ip = null)
    {
        $description = $success 
            ? "Login bem-sucedido para {$email}" 
            : "Tentativa de login falhada para {$email}";

        activity('auth')
            ->withProperties([
                'email' => $email,
                'success' => $success,
                'ip' => $ip ?? request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log($description);
    }

    /**
     * Logar alterações em configurações críticas
     */
    public static function logConfigChange($setting, $oldValue, $newValue)
    {
        activity('config')
            ->causedBy(auth()->user())
            ->withProperties([
                'setting' => $setting,
                'old_value' => $oldValue,
                'new_value' => $newValue
            ])
            ->log("Configuração '{$setting}' alterada");
    }

    /**
     * Logar transações financeiras
     */
    public static function logTransaction($type, $amount, $userId, $details = [])
    {
        activity('transaction')
            ->causedBy(auth()->user())
            ->performedOn(\App\Models\User::find($userId))
            ->withProperties(array_merge([
                'type' => $type,
                'amount' => $amount,
                'user_id' => $userId
            ], $details))
            ->log("Transação {$type} de {$amount} para usuário #{$userId}");
    }

    /**
     * Logar acessos a dados sensíveis
     */
    public static function logSensitiveDataAccess($dataType, $recordId = null)
    {
        activity('sensitive_access')
            ->causedBy(auth()->user())
            ->withProperties([
                'data_type' => $dataType,
                'record_id' => $recordId,
                'ip' => request()->ip(),
                'timestamp' => now()
            ])
            ->log("Acesso a dados sensíveis: {$dataType}");
    }
}