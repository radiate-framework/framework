<?php

namespace Radiate\Mail;

use Illuminate\Contracts\Support\Renderable;
use Radiate\Database\Models\User;
use Radiate\Support\Str;
use Radiate\Support\Facades\View;
use ReflectionClass;
use ReflectionProperty;
use WP_User;

abstract class Mailable implements Renderable
{
    /**
     * An array of to email addresses
     *
     * @var array
     */
    protected $to = [];

    /**
     * An array of carbon copy email addresses
     *
     * @var array
     */
    protected $cc = [];

    /**
     * An array of blind carbon copy email addresses
     *
     * @var array
     */
    protected $bcc = [];

    /**
     * An array of from email addresses
     *
     * @var array
     */
    protected $from = [];

    /**
     * An array of reply to email addresses
     *
     * @var array
     */
    protected $replyTo = [];

    /**
     * The email subject
     *
     * @var string
     */
    protected $subject;

    /**
     * The email html body
     *
     * @var string
     */
    protected $html;

    /**
     * The email plain text body
     *
     * @var string
     */
    protected $text;

    /**
     * Any email attachments
     *
     * @var array
     */
    protected $attachments = [];

    /**
     * Set a to address
     *
     * @param mixed $address
     * @param string|null $name
     * @return self
     */
    public function to($address, ?string $name = null): self
    {
        return $this->setAddress($address, $name, 'to');
    }

    /**
     * Set a carbon copy address
     *
     * @param mixed $address
     * @param string|null $name
     * @return self
     */
    public function cc($address, ?string $name = null): self
    {
        return $this->setAddress($address, $name, 'cc');
    }

    /**
     * Set a blind carbon copy address
     *
     * @param mixed $address
     * @param string|null $name
     * @return self
     */
    public function bcc($address, ?string $name = null): self
    {
        return $this->setAddress($address, $name, 'bcc');
    }

    /**
     * Set a from address
     *
     * @param mixed $address
     * @param string|null $name
     * @return self
     */
    public function from($address, ?string $name = null): self
    {
        return $this->setAddress($address, $name, 'from');
    }

    /**
     * Set a reply-to address
     *
     * @param mixed $address
     * @param string|null $name
     * @return self
     */
    public function replyTo($address, ?string $name = null): self
    {
        return $this->setAddress($address, $name, 'replyTo');
    }

    /**
     * Unset the recipients
     *
     * @return self
     */
    public function unsetRecipients(): self
    {
        foreach (['to', 'cc', 'bcc'] as $property) {
            $this->{$property} = [];
        }
        return $this;
    }

    /**
     * Set the subject
     *
     * @param string $subject
     * @return self
     */
    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Attach a file
     *
     * @param string $filepath
     * @return self
     */
    public function attach(string $filepath): self
    {
        $this->attachments[] = $filepath;

        return $this;
    }

    /**
     * Set the plain text body
     *
     * @param string $path
     * @param array $data
     * @return self
     */
    public function text(string $path, array $data = []): self
    {
        $this->text = View::make($path, $this->buildViewData($data))->render();

        return $this;
    }

    /**
     * Set the HTML body
     *
     * @param string $path
     * @param array $data
     * @return self
     */
    public function view(string $path, array $data = []): self
    {
        $this->html = View::make($path, $this->buildViewData($data))->render();

        return $this;
    }

    /**
     * Parse a markdown file and set the HTML and plain text bodies
     *
     * @param string $path
     * @param array $data
     * @return self
     */
    public function markdown(string $path, array $data = []): self
    {
        $this->text($path, $data);

        $this->html = View::make('mail.layout', [
            'markdown' => Str::markdown($this->text),
        ])->render();

        return $this;
    }

    /**
     * Merge the data passed to the view with the instance public properties.
     *
     * @param array $data
     * @return array
     */
    protected function buildViewData(array $data): array
    {
        $properties = [];

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->getDeclaringClass()->getName() !== self::class) {
                $properties[$property->getName()] = $property->getValue($this);
            }
        }
        return array_merge($properties, $data);
    }

    /**
     * Set an address on the specified property
     *
     * @param mixed $address
     * @param string|null $name
     * @param string $property
     * @return self
     */
    protected function setAddress($address, ?string $name = null, string $property = 'to'): self
    {
        foreach ($this->addressesToArray($address, $name) as $recipient) {
            $recipient = $this->normalizeRecipient($recipient);

            $this->{$property}[] = [
                'name'    => $recipient->name ?? null,
                'address' => $recipient->address,
            ];
        }

        return $this;
    }

    /**
     * Transform the addresses into a useable format
     *
     * @param mixed $address
     * @param string|null $name
     * @return array
     */
    protected function addressesToArray($address, ?string $name): array
    {
        if (!is_array($address)) {
            $address = is_string($name) ? [['name' => $name, 'address' => $address]] : [$address];
        }

        return $address;
    }

    /**
     * Normalise a recipient
     *
     * @param mixed $recipient
     * @return object
     */
    protected function normalizeRecipient($recipient): object
    {
        if ($recipient instanceof WP_User) {
            return (object) [
                'address' => $recipient->user_email,
                'name'    => $recipient->display_name ?? '',
            ];
        }
        if ($recipient instanceof User) {
            return (object) [
                'address' => $recipient->email,
                'name'    => $recipient->name ?? '',
            ];
        }
        if (is_array($recipient)) {
            return (object) $recipient;
        } elseif (is_string($recipient)) {
            return (object) ['address' => $recipient];
        }

        return $recipient;
    }

    /**
     * Format an email address and optional name
     *
     * @param array $recipient
     * @return string
     */
    protected function format(array $recipient): string
    {
        return trim("{$recipient['name']} <{$recipient['address']}>");
    }

    /**
     * Return an array of emails to send to
     *
     * @return array
     */
    public function buildTo(): array
    {
        return array_unique(array_map(function (array $to) {
            return $this->format($to);
        }, $this->to));
    }

    /**
     * Return the email subject or mailable classname
     *
     * @return string
     */
    public function buildSubject(): string
    {
        if ($this->subject) {
            return $this->subject;
        }
        return end(explode('\\', get_class($this)));
    }

    /**
     * Return a HTML message
     *
     * @return string
     */
    public function buildHtml(): string
    {
        return $this->html ?? $this->text;
    }

    /**
     * Determine if there is HTML set
     *
     * @return bool
     */
    public function hasHtml(): bool
    {
        return (bool) $this->html;
    }

    /**
     * Return a plain text message
     *
     * @return string
     */
    public function buildText(): string
    {
        return $this->text ?? '';
    }

    /**
     * Return an array of headers
     *
     * @return array
     */
    public function buildHeaders(): array
    {
        $headers = [];

        if (!empty($this->from)) {
            $headers[] = "From: {$this->format($this->from[0])}";
        }
        foreach (['cc' => 'Cc', 'bcc' => 'Bcc', 'replyTo' => 'Reply-To'] as $type => $header) {
            foreach ($this->{$type} as $recipient) {
                $headers[] = "{$header}: {$this->format($recipient)}";
            }
        }

        return array_unique($headers);
    }

    /**
     * Return an array of attachments
     *
     * @return array
     */
    public function buildAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Build the email
     *
     * @return \Radiate\Mail\Mailable
     */
    public function build(): self
    {
        return $this;
    }

    /**
     * Render the email
     *
     * @return string
     */
    public function render(): string
    {
        return $this->build()->buildHtml();
    }

    /**
     * Return the email render
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
