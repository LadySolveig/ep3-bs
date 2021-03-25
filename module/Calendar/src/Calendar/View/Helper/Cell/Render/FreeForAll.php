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

            if ($square->getMeta('public_names', 'false') == 'true') {
                $cellLabel = $booking->needExtra('user')->need('alias');
            } else if ($square->getMeta('private_names', 'false') == 'true' && $user) {
                $cellLabel = $booking->needExtra('user')->need('alias');
            } else {
                $cellLabel = null;
            }

                    if (! $cellLabel) {
                            $cellLabel = $this->view->t('Free');
            }		

            return $view->calendarCellLink($cellLabel, $view->url('square', [], $cellLinkParams), 'cc-free cc-free-partially' . $cellGroup);
        } else {
            $labelFree = $square->getMeta('label.free', 'Still free');

            return $view->calendarCellLink($labelFree, $view->url('square', [], $cellLinkParams), 'cc-free cc-free-partially');
        }
    }
}