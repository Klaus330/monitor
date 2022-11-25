<?php

namespace App\Mail;

use App\Models\Site;
use App\Repositories\SiteRouteRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrokenLinksReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(protected Site $site, protected SiteRouteRepository $siteRouteRepository)
    {
        //
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('claudiupopa330@gmail.com', 'Claudiu Popa'),
            subject: 'Broken Links Report',
            to: $this->site->owner->email
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.broken_links_report',
            with: [
                'site' => $this->site,
                'brokenLinksCount' => $this->siteRouteRepository->getBrokenLinksCount($this->site->id)
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return Attachment::fromData(function () {
            return $this->siteRouteRepository->generateCsvReportWithBrokenLinks($this->site);
        }, 'report.csv');
    }
}
