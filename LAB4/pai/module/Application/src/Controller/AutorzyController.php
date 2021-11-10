<?php

namespace Application\Controller;

use Application\Form\AutorForm;
use Application\Model\Autor;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AutorzyController extends AbstractActionController
{
    public function __construct(public Autor $autor, public AutorForm $autorForm)
    {
    }

    public function listaAction()
    {
        return [
            'autorzy' => $this->autor->pobierzWszystko(),
        ];
    }

    public function szczegolyAction()
    {
        $id = (int)$this->params()->fromRoute('id');
        if (empty($id)) {
            $this->redirect()->toRoute('autorzy');
        }
        foreach ($this->autorForm->getElements() as $my_elements) {
            $my_elements->setAttributes(['readonly' => 'readonly', 'disabled' => true]);
        }
        $this->autorForm->get('zapisz')->setValue('Wróć')->setAttribute('disabled', false);
        $request = $this->getRequest();
        if ($request->isPost()) {
            return $this->redirect()->toRoute('autorzy');
        } else {
            $daneAutorzy = $this->autor->pobierz($id);
            $this->autorForm->setData($daneAutorzy);
        }
        $viewModel = new ViewModel(['tytul' => 'Szczegóły autora', 'form' => $this->autorForm]);
        $viewModel->setTemplate('application/autorzy/dodaj');
        return $viewModel;
    }

    public function dodajAction()
    {
        $this->autorForm->get('zapisz')->setValue('Dodaj');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->autorForm->setData($request->getPost());

            if ($this->autorForm->isValid()) {
                $this->autor->dodaj($request->getPost());

                return $this->redirect()->toRoute('autorzy');
            }
        }
        return ['tytul' => 'Dodawanie autora', 'form' => $this->autorForm];
    }

    public function edytujAction()
    {
        $id = (int)$this->params()->fromRoute('id');
        if (empty($id)) {
            $this->redirect()->toRoute('autorzy');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->autorForm->setData($request->getPost());

            if ($this->autorForm->isValid()) {
                $this->autor->aktualizuj($id, $request->getPost());
                return $this->redirect()->toRoute('autorzy');
            }
        } else {
            $daneAutorzy = $this->autor->pobierz($id);
            $this->autorForm->setData($daneAutorzy);
        }
        $viewModel = new ViewModel(['tytul' => 'Edytuj autora', 'form' => $this->autorForm]);
        $viewModel->setTemplate('application/autorzy/dodaj');

        return $viewModel;
    }

    public function usunAction()
    {
        $id = (int)$this->params()->fromRoute('id');
        if (empty($id)) {
            $this->redirect()->toRoute('autorzy');
        }
        foreach ($this->autorForm->getElements() as $elem) {
            $elem->setAttributes(['readonly' => 'readonly', 'disabled' => true]);
        }
        $this->autorForm->get('zapisz')->setValue('Usuń')->setAttribute('disabled', false);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $this->autor->usun($id);
            return $this->redirect()->toRoute('autorzy');
        } else {
            $autorzyInfo = $this->autor->pobierz($id);
            $this->autorForm->setData($autorzyInfo);
        }
        $viewModel = new ViewModel(['tytul' => 'Czy chcesz usunąć autora wraz z jego autorskimi książkami?', 'form' => $this->autorForm]);
        $viewModel->setTemplate('application/autorzy/dodaj');
        return $viewModel;
    }
}