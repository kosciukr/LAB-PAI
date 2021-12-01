<?php
namespace Nieruchomosci\Model;

use Laminas\Db\Adapter as DbAdapter;
use Laminas\Db\Sql\Sql;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Session\SessionManager;
use Mpdf\Mpdf;

class Oferta implements DbAdapter\AdapterAwareInterface
{
    use DbAdapter\AdapterAwareTrait;

    public function __construct(public PhpRenderer $phpRenderer)
    {
    }

    /**
     * Pobiera obiekt Paginator dla przekazanych parametrów.
     *
     * @param array $szukaj
     * @return \Laminas\Paginator\Paginator
     */
    public function pobierzWszystko(array $szukaj = []): Paginator
    {
        $session = new SessionManager();
        $sessionID = $session->getId();
        $dbAdapter = $this->adapter;

        $sql = new Sql($dbAdapter);
        $select = $sql->select('koszyk')->where(['id_sesji' => "{$sessionID}"])->columns(['id_oferty']);
        $selectString = $sql->buildSqlString($select);
        $ofertWKoszyku = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);

        $kosz = array();
        foreach ($ofertWKoszyku as $row) {
            $kosz[] = $row['id_oferty'];
        }

        $sql = new Sql($dbAdapter);
        $select = $sql->select();
        $select->from('oferty')->where->notIn('id', $kosz);

        if (!empty($szukaj['typ_oferty'])) {
            $select->where(['typ_oferty' => $szukaj['typ_oferty']]);
        }
        if (!empty($szukaj['typ_nieruchomosci'])) {
            $select->where(['typ_nieruchomosci' => $szukaj['typ_nieruchomosci']]);
        }
        if (!empty($szukaj['numer'])) {
            $select->where(['numer' => $szukaj['numer']]);
        }
        if (!empty($szukaj['powierzchnia'])) {
            $select->where(['powierzchnia' => $szukaj['powierzchnia']]);
        }
        if (!empty($szukaj['cena'])) {
            $select->where(['cena' => $szukaj['cena']]);
        }

        $select->order('id');
        $paginatorAdapter = new DbSelect($select, $dbAdapter);
        return new Paginator($paginatorAdapter);
    }

    /**
     * Pobiera dane jednej oferty.
     *
     * @param int $id
     * @return array
     */
    public function pobierz(int $id)
    {
        $dbAdapter = $this->adapter;

        $sql = new Sql($dbAdapter);
        $select = $sql->select('oferty');
        $select->where(['id' => $id]);

        $selectString = $sql->buildSqlString($select);
        $wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);

        return $wynik->count() ? $wynik->current() : [];
    }

    /**
     * Generuje PDF z danymi oferty.
     *
     * @param $oferta
     * @throws \Mpdf\MpdfException
     */
    public function drukuj($oferta): void
    {
        $vm = new ViewModel(['oferta' => $oferta]);
        $vm->setTemplate('nieruchomosci/oferty/drukuj');
        $html = $this->phpRenderer->render($vm);

        $mpdf = new Mpdf(['tempDir' => getcwd() . '/data/temp']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('oferta.pdf', 'D');
    }
}
