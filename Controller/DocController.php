<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Documentation Controller.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class DocController extends Controller
{
    /**
     * Documentation Homepage.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('ASFProductBundle:Doc:index.html.twig');
    }
}
