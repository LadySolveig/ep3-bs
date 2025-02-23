<?php

namespace Backend\View\Helper\Booking;

use Booking\Entity\Reservation;
use Square\Manager\SquareManager;
use Zend\View\Helper\AbstractHelper;

class BookingFormat extends AbstractHelper
{

    protected $squareManager;

    public function __construct(SquareManager $squareManager)
    {
        $this->squareManager = $squareManager;
    }

    public function __invoke(Reservation $reservation, $dateStart = null, $dateEnd = null, $search = null)
    {
        $view = $this->getView();
        $html = '';

        $booking = $reservation->needExtra('booking');

        switch ($booking->need('status')) {
            case 'cancelled':
                $attr = ' class="gray"';
                break;
            default:
                $attr = null;
                break;
        }

        $html .= sprintf('<tr %s>', $attr);

        $html .= sprintf('<td class="status-col right-text no-print">%s</td>',
            $view->t($booking->getStatus()));

        $html .= sprintf('<td class="no-print">%s</td>',
            $booking->need('bid'));

        if ($booking->getExtra('user')) {
            $userName = $booking->getExtra('user')->get('alias');
            $userNameFull = $booking->getExtra('user')->getMeta('lastname') . ', ' . $booking->getExtra('user')->getMeta('firstname');
            $userEMail = $booking->getExtra('user')->get('email');
        } else {
            $userName = $booking->need('uid');
        }

        $html .= sprintf('<td class="no-print"><b>%s</b></td>',
            $userName);
        
        $html .= sprintf('<td class="print-only"><b>%s</b></td>',
            $userNameFull);
        
        $html .= sprintf('<td class="print-only">%s</td>',
            $userEMail);

        /* Date and time col */

        $date = new \DateTime($reservation->get('date'));

        $fullDate = $view->dateFormat($date, \IntlDateFormatter::FULL);
        $fullDateParts = explode(', ', $fullDate);

        $html .= sprintf('<td>%s</td>',
        substr($fullDateParts[0],0,2));

        $html .= sprintf('<td>%s</td>',
            $view->dateFormat($date, \IntlDateFormatter::MEDIUM));

        $html .= sprintf('<td>%s</td>',
            $view->timeRange($reservation->get('time_start'), $reservation->get('time_end'), '%s to %s'));

        /* Square col */

        if ($booking->get('sid')) {
            $squareName = $this->squareManager->get($booking->get('sid'))->get('name');
        } else {
            $squareName = '-';
        }

        $html .= sprintf('<td>%s</td>',
            $squareName);

        /* Notes col */

        $notes = $booking->getMeta('notes');

        if ($notes) {
            if (strlen($notes) > 48) {
                $notes = substr($notes, 0, 48) . '&hellip;';
            }

            $notes = '<span class="small-text">' . $notes . '</span>';
        } else {
            $notes = '-';
        }

        $html .= sprintf('<td class="no-print notes-col">%s</td>',
            $notes);

        /* Actions col */

        if ($booking->get('status') == 'cancelled') {

            $html .= sprintf('<td class="actions-col no-print"><a href="%s" class="unlined gray symbolic symbolic-edit">%s</a></td>',
                $view->url('backend/booking/edit', [], ['query' => [
                    'ds' => $date->format('Y-m-d'),
                    'ts' => substr($reservation->get('time_start'), 0, 5),
                    'te' => substr($reservation->get('time_end'), 0, 5),
                    's' => $booking->get('sid'),
                    'r' => $reservation->get('rid')]]),
                $view->t('Edit'));

        } else {

            $html .= sprintf('<td class="actions-col no-print"><a href="%s" class="unlined gray symbolic symbolic-edit">%s</a></td>',
                $view->url('backend/booking/edit', [], ['query' => [
                    'ds' => $date->format('Y-m-d'),
                    'ts' => substr($reservation->get('time_start'), 0, 5),
                    'te' => substr($reservation->get('time_end'), 0, 5),
                    's' => $booking->get('sid'),
                    'r' => $reservation->get('rid')]]),
                $view->t('Edit'));
        }

        $html .= '</tr>';

        return $html;
    }

}
