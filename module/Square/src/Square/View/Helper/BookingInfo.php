<?php

namespace Square\View\Helper;

use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class BookingInfo extends AbstractHelper
{

    public function __invoke(Square $square, array $bookings)
    {
        $outputPlayerNames = null;
        $outputNotes = [];
        // Mitspieler holen
        foreach ($bookings as $booking) {
            $reservationOwner = $booking->getExtra('user')->get('alias');
            if ($outputPlayerNames === null) {
                $outputPlayerNames = $reservationOwner;
            } else {
                $outputPlayerNames .= sprintf(', %1$s', $reservationOwner);
            }
            
            $playerNames = $booking->getMeta('player-names');
            if ($playerNames) {
                $playerNames = @unserialize($playerNames);
                if ($playerNames) {
                    foreach ($playerNames as $playerData) {
                        $nameData = trim($playerData['value']);
                        if ($nameData !== "") {
                            if ($outputPlayerNames === null) {
                                $outputPlayerNames = $nameData;
                            } else {
                                $outputPlayerNames .= sprintf(', %1$s', $nameData);
                            }
                        }
                    }
                }
            }
            
            if (!empty($note = $booking->getMeta('notes')) && trim($note) !== 'Anmerkungen des Benutzers:') {            
                $outputNotes[] = str_replace('Anmerkungen des Benutzers:','Info ' . $reservationOwner . ':<br>',$note);
            }
            
        }
        
        
        
        if (isset($outputPlayerNames) && $outputPlayerNames != null) {
            // return sprintf('<br />(%1$s)', $outputPlayerNames);
            $output = sprintf('<p><span class="yellow">(%1$s)</span></p>'
                    , $outputPlayerNames);
            if(!empty($outputNotes)) {
                $output .= '<p style="text-align:left;"><span><i>';
                $output .= implode('</i></span></p><p><span><i>', $outputNotes);
                $output .= '</i></span></p>';
            }
            return $output;
        }        

        return null;
    }

}