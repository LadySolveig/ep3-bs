<?php

namespace Square\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class SquareController extends AbstractActionController
{

    public function indexAction()
    {
        $dateStartParam = $this->params()->fromQuery('ds');
        $dateEndParam = $this->params()->fromQuery('de');
        $timeStartParam = $this->params()->fromQuery('ts');
        $timeEndParam = $this->params()->fromQuery('te');
        $squareParam = $this->params()->fromQuery('s');
        $flagParam = $this->params()->fromQuery('f');

        $serviceManager = @$this->getServiceLocator();
        $squareProductManager = $serviceManager->get('Square\Manager\SquareProductManager');
        $squareValidator = $serviceManager->get('Square\Service\SquareValidator');
       if (empty($timeEndParam)) {
            $square = $serviceManager->get('Square\Manager\SquareManager')->get($squareParam);
            $bookable = $square->need('time_block');
            $datetimeStart = \DateTime::createFromFormat('Y-m-d H:i:s', $dateStartParam . ' '. $timeStartParam . ':00');
            $datetimeEnd = clone $datetimeStart;
            $timeEndParam = $datetimeEnd->modify('+' . $bookable . ' sec')->format('H:i');
            $isBookable = $squareValidator->isBookable($dateStartParam, $dateEndParam, $timeStartParam, $timeEndParam, $squareParam);
            if($isBookable['bookable'] == false) {
                $bookable = $square->need('time_block_bookable');
                $datetimeEnd = clone $datetimeStart;
                $timeEndParam = $datetimeEnd->modify('+' . $bookable . ' sec')->format('H:i');
            }
        }

        $byproducts = $squareValidator->isBookable($dateStartParam, $dateEndParam, $timeStartParam, $timeEndParam, $squareParam);
        $byproducts['validator'] = $squareValidator;

        $products = $squareProductManager->getBySquare($byproducts['square']);
        $byproducts['products'] = $products;

        $byproducts['flag'] = $flagParam;

        return $this->ajaxViewModel($byproducts);
    }

}
