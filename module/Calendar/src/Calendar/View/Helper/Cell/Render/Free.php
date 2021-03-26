<?php

namespace Calendar\View\Helper\Cell\Render;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class Free extends AbstractHelper
{

    public function __invoke($user, $userBooking, array $reservations, array $cellLinkParams, Square $square)
    {
        $view = $this->getView();

	    $labelFree = $square->getMeta('label.free', $this->view->t('Free'));

        if ($user && $user->can('calendar.see-data, calendar.create-single-bookings, calendar.create-subscription-bookings')) {
            return $view->calendarCellRenderFreeForPrivileged($reservations, $cellLinkParams, $square);
        } else if ($user) {
            if ($userBooking) {
                $cellLabel = $view->t('Your Booking');                
                $cellGroup = ' cc-group-' . $userBooking->need('bid');
                if (count($reservations) > 0 && ($square->getMeta('public_names', 'false') == 'true' || $square->getMeta('private_names', 'false') == 'true' && $user)) {
                    foreach ($reservations as $reservation) {
                       $booking = $reservation->needExtra('booking'); 
                       $cellLabel .= ($booking->get('uid') != $user->get('uid')) ? ' * ' . $booking->needExtra('user')->need('alias') : '';
                    }
                }

                return $view->calendarCellLink($cellLabel, $view->url('square', [], $cellLinkParams), 'cc-own' . $cellGroup);
            } else {
                return $view->calendarCellRenderFreeForAll($reservations, $cellLinkParams, $square, $user);
//                return $view->calendarCellLink($labelFree, $view->url('square', [], $cellLinkParams), 'cc-free');
            }
        } else {
            return $view->calendarCellRenderFreeForAll($reservations, $cellLinkParams, $square);
//            return $view->calendarCellLink($labelFree, $view->url('square', [], $cellLinkParams), 'cc-free');
        }
    }

}
