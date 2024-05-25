<?php
/**
* CG Chat Component  - Joomla 4.x/5.x Component
* Version			: 1.0.0
* Package			: CG Chat
* copyright 		: Copyright (C) 2024 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : Kide ShoutBox
*/

namespace ConseilGouz\Component\CGChat\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class IconsModel extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'code', 'a.code',
                'img', 'a.img',
                'ordering', 'a.ordering',
            );
        }

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db		= $this->getDatabase();
        $query	= $db->getQuery(true);
        $orderCol	= $this->state->get('list.ordering');
        if (!$orderCol) {
            $orderCol = 'a.id';
        }
        $orderDirn	= $this->state->get('list.direction');
        if (!$orderDirn) {
            $orderDirn = 'ASC';
        }
        $query->select('a.*')
        ->from('#__cgchat_icons as a')
        ->order($db->escape($orderCol.' '.$orderDirn));
        return $query;

    }
}
