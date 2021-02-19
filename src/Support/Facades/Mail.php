<?php

namespace Radiate\Support\Facades;

/**
 * @method static void alwaysFrom(string $address, ?string $name = null) Set the global from address and name.
 * @method static void alwaysReplyTo(string $address, ?string $name = null) Set the global reply-to address and name.
 * public function void alwaysTo(string $address, ?string $name = null) Set the global to address and name.
 * @method static \Radiate\Mail\PendingMail to(mixed $users) Begin the process of mailing a mailable class instance.
 * @method static \Radiate\Mail\PendingMail cc(mixed $users) Begin the process of mailing a mailable class instance.
 * @method static \Radiate\Mail\PendingMail bcc(mixed $users) Begin the process of mailing a mailable class instance.
 * @method static string render(\Radiate\Mail\Mailable $mailable) Render the given message as a view.
 * @method static void send(\Radiate\Mail\Mailable $mailable) Send the mail
 *
 * @see \Radiate\Mail\Mailer
 */
class Mail extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mailer';
    }
}
