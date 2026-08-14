<?php

namespace App\Repository\Supplier;

use App\Entity\Supplier\Supplier;
use App\Service\Core\CoreUtils;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Supplier>
 *
 * @method Supplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Supplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Supplier[]    findAll()
 * @method Supplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supplier::class);
    }

    public function add(Supplier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Supplier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPaginatedQuery(array $searchFormData = [], array $languages = []) {
        $qb = $this->createQueryBuilder('t');

        if (!empty($languages)) {
            foreach ($languages as $language) {
                $qb->leftJoin('t.lngs', $language, "WITH", "{$language}.lng='{$language}'")
                    ->addSelect($language);
            }
        }
        if (!empty($searchFormData['name'])) {
            $qb->andWhere("(t.name LIKE :name)")->setParameter('name', "%{$searchFormData['name']}%");
        }
        if (!empty($searchFormData['eek'])) {
            $qb->andWhere("t.eek LIKE :eek")->setParameter('eek', "%{$searchFormData['eek']}%");
        }
        if (!empty($searchFormData['vat'])) {
            $qb->andWhere("t.vat LIKE :vat")->setParameter('vat', "%{$searchFormData['vat']}%");
        }
        if (!empty($searchFormData['responsiblePerson'])) {
            $qb->andWhere("t.responsiblePerson LIKE :responsiblePerson")->setParameter('responsiblePerson', "%{$searchFormData['responsiblePerson']}%");
        }
        if (!empty($searchFormData['email'])) {
            $qb->andWhere("t.email LIKE :email")->setParameter('email', "%{$searchFormData['email']}%");
        }
        if (!empty($searchFormData['phone'])) {
            $qb->andWhere("t.phone LIKE :phone")->setParameter('phone', "%{$searchFormData['phone']}%");
        }

        if(isset($searchFormData[CoreUtils::$SORT_LIST_QUERY_NAME])) {

            foreach ($searchFormData[CoreUtils::$SORT_LIST_QUERY_NAME] as $sortColumn=>$sortValue) {

                $qbSortColumn = '';
                if($sortColumn == 'name') {
                    $qbSortColumn = 't.name';
                }

                if(!empty($qbSortColumn)) {
                    $qb->addOrderBy($qbSortColumn, $sortValue);
                }
            }

        }

        return $qb->getQuery();
    }
} 