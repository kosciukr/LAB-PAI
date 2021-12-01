<?php
namespace Nieruchomosci\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Nieruchomosci\Model\Koszyk;

class KoszykController extends AbstractActionController
{
    /**
     * KoszykController constructor.
     *
     * @param Koszyk $koszyk
     */
    public function __construct(public Koszyk $koszyk)
    {
    }

    public function dodajAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->koszyk->dodaj($this->params('id'));
            $this->getResponse()->setContent('ok');
        }

        return $this->getResponse();
    }

    public function usunAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->koszyk->usun($this->params('id'));
            $this->getResponse()->setContent('ok');
        }

        return $this->getResponse();
    }

    public function listaAction()
    {
        return [
            'koszyk' => $this->koszyk->pobierzWszystko(),
        ];
    }

    public function drukujAction()
    {
        $koszyk = $this->koszyk->pobierzWszystko();

        if ($koszyk) {
            $this->koszyk->drukuj($koszyk);
        }

        return $this->getResponse();
    }
}
