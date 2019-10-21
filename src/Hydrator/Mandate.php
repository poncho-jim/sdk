<?php

declare(strict_types=1);

namespace PayNL\Sdk\Hydrator;

use DateTime as stdDateTime, Exception;
use PayNL\Sdk\DateTime;
use Zend\Hydrator\ClassMethods;
use PayNL\Sdk\Hydrator\{
    BankAccount as BankAccountHydrator,
    Statistics as StatisticsHydrator,
    Customer as CustomerHydrator
};
use PayNL\Sdk\Model\{
    Amount,
    BankAccount,
    Mandate as MandateModel,
    Statistics,
    Interval,
    Customer
};

/**
 * Class Mandate
 *
 * @package PayNL\Sdk\Hydrator
 */
class Mandate extends AbstractHydrator
{
    /**
     * @inheritDoc
     *
     * @throws Exception
     *
     * @return MandateModel
     */
    public function hydrate(array $data, $object): MandateModel
    {
        $this->validateGivenObject($object, MandateModel::class);

        $data['description'] = $data['description'] ?? '';

        if (true === array_key_exists('processDate', $data) && false === ($data['processDate'] instanceof DateTime)) {
            $processDate = $data['processDate'];
            if ($processDate instanceof stdDateTime) {
                $processDate = $processDate->format(stdDateTime::ATOM);
            }
            $data['processDate'] = DateTime::createFromFormat(DateTime::ATOM, $processDate);
        }

        if (true === array_key_exists('amount', $data)) {
            $data['amount'] = (new ClassMethods())->hydrate($data['amount'], new Amount());
        }

        if (true === array_key_exists('statistics', $data)) {
            $data['statistics'] = (new StatisticsHydrator())->hydrate($data['statistics'], new Statistics());
        }

        if (true === array_key_exists('interval', $data)) {
            $data['interval'] = (new ClassMethods())->hydrate($data['interval'], new Interval());
        }

        if (true === array_key_exists('customer', $data)) {
            $data['customer'] = (new CustomerHydrator())->hydrate($data['customer'], new Customer());
        }

        /** @var MandateModel $mandate */
        $mandate = parent::hydrate($data, $object);
        return $mandate;
    }
}
