<?php
declare(strict_types=1);

namespace Application\Controller;

use Application\Model;
use JetBrains\PhpStorm\Pure;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private Model\Miesiace $miesiace;
    private Model\Liczby $liczby;
    #[Pure] public function __construct()
    {
        $this->miesiace = new Model\Miesiace();
        $this->liczby = new Model\Liczby();
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function miesiaceAction(): ViewModel
    {
        return new ViewModel([
            'miesiace' => $this->miesiace->pobirzWszystkie(),
        ]);
    }
    public function liczbyAction(): ViewModel
    {
        return new ViewModel([
            'liczby' => $this->liczby->generuj(),
        ]);
    }
}