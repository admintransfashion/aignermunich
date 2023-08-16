<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Reservation\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\Reservation\Cron\AutoCancel as cronAutoCancel;
use Magento\Framework\App\State;

class AutoCancel extends Command
{
    /**
     * @var \Trans\IntegrationCatalogPrice\Cron\Pim\Save\Price
     */
    protected $cronAutoCancel;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;
    /**
     * Constructor method
     * @param cronPrice $cronPrice
     * @param State $state
     */
    public function __construct(
        cronAutoCancel $cronAutoCancel,
        State $state
    ) {
        $this->cronAutoCancel = $cronAutoCancel;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * Console configure
     * @return void
     */
    protected function configure()
    {
        $this->setName('integration:cron:autocancel');
        $this->setDescription('auto cancel');
        parent::configure();
    }

    /**
        * Console execution
        * @param  InputInterface  $input
        * @param  OutputInterface $output
        * @return void
        * @throw \Exception
        */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $this->cronAutoCancel->execute();
    }
}
