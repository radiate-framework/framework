<?php

namespace Radiate\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use Radiate\Events\Dispatcher;

class Mailer
{
    /**
     * The event dispatcher instance.
     *
     * @var \Radiate\Events\Dispatcher
     */
    protected $events;

    /**
     * The global from address and name.
     *
     * @var array
     */
    protected $from;

    /**
     * The global reply-to address and name.
     *
     * @var array
     */
    protected $replyTo;

    /**
     * The global to address and name.
     *
     * @var array
     */
    protected $to;

    /**
     * Create a new Mailer instance.
     *
     * @param  \Radiate\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Set the global from address and name.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return void
     */
    public function alwaysFrom(string $address, ?string $name = null): void
    {
        $this->from = compact('address', 'name');
    }

    /**
     * Set the global reply-to address and name.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return void
     */
    public function alwaysReplyTo(string $address, ?string $name = null): void
    {
        $this->replyTo = compact('address', 'name');
    }

    /**
     * Set the global to address and name.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return void
     */
    public function alwaysTo(string $address, ?string $name = null): void
    {
        $this->to = compact('address', 'name');
    }

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Radiate\Mail\PendingMail
     */
    public function to($users): PendingMail
    {
        return (new PendingMail($this))->to($users);
    }

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Radiate\Mail\PendingMail
     */
    public function cc($users): PendingMail
    {
        return (new PendingMail($this))->cc($users);
    }

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Radiate\Mail\PendingMail
     */
    public function bcc($users): PendingMail
    {
        return (new PendingMail($this))->bcc($users);
    }

    /**
     * Render the given message as a view.
     *
     * @param  \Radiate\Mail\Mailable $mailable
     * @return string
     */
    public function render(Mailable $mailable): string
    {
        return $mailable->render();
    }

    /**
     * Send the mail
     *
     * @param \Radiate\Mail\Mailable $mailable
     * @return void
     */
    public function send(Mailable $mailable): void
    {
        $mailable->build();

        $this->events->listen('phpmailer_init', function (PHPMailer $phpMailer) use ($mailable) {
            if ($mailable->hasHtml()) {
                $phpMailer->AltBody = $mailable->buildText();
            }

            $phpMailer->CharSet = 'UTF-8';
            $phpMailer->Encoding = 'base64';
        });

        $this->events->listen('wp_loaded', function () use ($mailable) {
            return wp_mail(
                $mailable->buildTo(),
                $mailable->buildSubject(),
                $mailable->buildHtml(),
                $mailable->buildHeaders(),
                $mailable->buildAttachments()
            );
        });
    }

    /**
     * Set the global "to" address on the given message.
     *
     * @param  \Radiate\Mail\PendingMail $mailable
     * @return void
     */
    protected function setGlobalToAndRemoveCcAndBcc(Mailable $mailable): void
    {
        $mailable->unsetRecipients()
            ->to($this->to['address'], $this->to['name']);
    }
}
