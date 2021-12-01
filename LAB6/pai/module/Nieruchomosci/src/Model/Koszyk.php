<?php
namespace Nieruchomosci\Model;

use Laminas\Db\Adapter as DbAdapter;
use Laminas\Db\Sql\Sql;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Session\SessionManager;
use Mpdf\Mpdf;

class Koszyk implements DbAdapter\AdapterAwareInterface
{
	use DbAdapter\AdapterAwareTrait;
	
	protected Container $sesja;
	
	public function __construct(public PhpRenderer $phpRenderer)
	{
		$this->sesja = new Container('koszyk');
		$this->sesja->liczba_ofert = $this->sesja->liczba_ofert ?: 0;
	}

    /**
     * Dodaje ofertdo koszyka.
     *
     * @param int $idOferty
     * @return int|null
     */
	public function dodaj(int $idOferty): ?int
	{
		$dbAdapter = $this->adapter;
		$session = new SessionManager();
		
		$sql = new Sql($dbAdapter);
		$insert = $sql->insert('koszyk');
		$insert->values([
			'id_oferty' => $idOferty,
			'id_sesji' => $session->getId()
        ]);
		
		$selectString = $sql->buildSqlString($insert);
		$wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
		
		$this->sesja->liczba_ofert++;
		
		try {
			return $wynik->getGeneratedValue();
		} catch(\Exception $e) {
			return null;
		}
	}

	public function usun(int $idOferty): ?int
	{
		$dbAdapter = $this->adapter;
		$session = new SessionManager();
		
		$sql = new Sql($dbAdapter);
		$delete = $sql->delete('koszyk');
		$delete -> where(['id_sesji' => $session->getId()]);
		$delete -> where(['id_oferty' => $idOferty]);
		
		$selectString = $sql->buildSqlString($delete);
		$wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
		
		$this->sesja->liczba_ofert--;
		
		try {
			return $wynik->getGeneratedValue();
		} catch(\Exception $e) {
			return null;
		}
	}

    /**
     * Zwraca liczbe ofert w koszyku.
     *
     * @return int
     */
	public function liczbaOfert(): int
	{
		return $this->sesja->liczba_ofert;
	}


	public function pobierzWszystko()
    {
        $dbAdapter = $this->adapter;
		$session = new SessionManager();
        $sql = new Sql($dbAdapter);

        $select = $sql->select();
		$select->from(['k' => 'koszyk']);
        $select->join(['o' => 'oferty'], 'k.id_oferty = o.id', ['typ_oferty', 'typ_nieruchomosci', 'numer', 'powierzchnia', 'cena']);
		$select->where(['id_sesji' => $session->getId()]);
		$select->order('k.id_oferty');

        $selectString = $sql->buildSqlString($select);
        $wynik = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);

        return $wynik;
    }

	public function drukuj($koszyk): void
    {
        $vm = new ViewModel(['koszyk' => $koszyk]);
        $vm->setTemplate('nieruchomosci/koszyk/drukuj');
        $html = $this->phpRenderer->render($vm);

        $mpdf = new Mpdf(['tempDir' => getcwd() . '/data/temp']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('ofertaKoszyk.pdf', 'D');
    }
}