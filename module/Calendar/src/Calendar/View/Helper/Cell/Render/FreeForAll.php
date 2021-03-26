<?php

namespace Calendar\View\Helper\Cell\Render;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class FreeForAll extends AbstractHelper
{
    public function __invoke(array $reservations, array $cellLinkParams, Square $square, $user = null)
    {
        $view = $this->getView();

        $reservationsCount = count($reservations);

        if ($reservationsCount == 0) {
            $labelFree = $square->getMeta('label.free', $this->view->t('Free'));

            return $view->calendarCellLink($labelFree, $view->url('square', [], $cellLinkParams), 'cc-free');
        } else if ($reservationsCount == 1) {
            $reservation = current($reservations);
            $booking = $reservation->needExtra('booking');

            $cellGroup = ' cc-group-' . $booking->need('bid');
            $ccNote = '';

            $cellLabel = $this->view->t('Free');
            if ($square->getMeta('public_names', 'false') == 'true') {
                $cellLabel .= ' * ' . $booking->needExtra('user')->need('alias'); 
                $ccNote = (empty($note = $booking->getMeta('notes')) || trim($note) === 'Anmerkungen des Benutzers:') ? '' : ' cc-note';
            } elseif ($square->getMeta('private_names', 'false') == 'true' && $user) {
                $cellLabel .= ' * ' . $booking->needExtra('user')->need('alias');
                $ccNote = (empty($note = $booking->getMeta('notes')) || trim($note) === 'Anmerkungen des Benutzers:') ? '' : ' cc-note';
            }	

            return $view->calendarCellLink($cellLabel, $view->url('square', [], $cellLinkParams), 'cc-free cc-free-partially' . $cellGroup . $ccNote);
        } else {
            $cellLabel = $this->view->t('Free');
            $ccNote = '';
            if (count($reservations) > 0 && ($square->getMeta('public_names', 'false') == 'true' || $square->getMeta('private_names', 'false') == 'true' && $user)) {
                foreach ($reservations as $reservation) {
                   $booking = $reservation->needExtra('booking'); 
                   $cellLabel .= ' * ' . $booking->needExtra('user')->need('alias');
                   $ccNote = ((empty($note = $booking->getMeta('notes')) || trim($note) === 'Anmerkungen des Benutzers:')  && empty($ccNote)) ? '' : ' cc-note';
                }
            } 
            return $view->calendarCellLink($cellLabel, $view->url('square', [], $cellLinkParams), 'cc-free cc-free-partially' . $ccNote);
        }
    }
}