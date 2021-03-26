<?php

namespace Calendar\View\Helper\Cell\Render;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class Occupied extends AbstractHelper
{

    public function __invoke($user, $userBooking, array $reservations, array $cellLinkParams, Square $square)
    {
        $view = $this->getView();

        if ($user && $user->can('calendar.see-data')) {
            return $view->calendarCellRenderOccupiedForPrivileged($reservations, $cellLinkParams);
        } else if ($user) {
            if ($userBooking) {
                $cellLabel = $view->t('Your Booking');
                $cellGroup = ' cc-own cc-group-' . $userBooking->need('bid'); 
            } else {
                $cellLabel = $this->view->t('Occupied');
                $cellGroup = ' cc-single';
            }
            if (count($reservations) > 0 && ($square->getMeta('public_names', 'false') == 'true' || $square->getMeta('private_names', 'false') == 'true' && $user)) {
                foreach ($reservations as $reservation) {
                   $booking = $reservation->needExtra('booking'); 
                   $cellLabel .= (!$userBooking || ($booking->get('uid') != $user->get('uid'))) ? ' * ' . $booking->needExtra('user')->need('alias') :'';
                }
            }
            return $view->calendarCellLink($cellLabel, $view->url('square', [], $cellLinkParams), $cellGroup);
        } else {
            return $view->calendarCellRenderOccupiedForVisitors($reservations, $cellLinkParams, $square);
        }
    }

}
