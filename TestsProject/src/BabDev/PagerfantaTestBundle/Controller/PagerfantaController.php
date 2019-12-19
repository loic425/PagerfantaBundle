<?php

namespace BabDev\PagerfantaTestBundle\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\FixedAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PagerfantaController extends AbstractController
{
    /**
     * @Route("/pagerfanta/default-view", name="pagerfanta_default_view")
     */
    public function defaultViewAction()
    {
        return $this->renderPagerfanta('defaultView');
    }

    /**
     * @Route("/pagerfanta/default-short-view", name="pagerfanta_default_short_view")
     */
    public function defaultShortViewAction()
    {
        return $this->renderPagerfanta('defaultShortView');
    }

    /**
     * @Route("/pagerfanta/twitter-bootstrap-view", name="pagerfanta_twitter_bootstrap_view")
     */
    public function twitterBootstrapViewAction()
    {
        return $this->renderPagerfanta('twitterBootstrapView');
    }

    /**
     * @Route("/pagerfanta/twitter-bootstrap3-view", name="pagerfanta_twitter_bootstrap3_view")
     */
    public function twitterBootstrap3ViewAction()
    {
        return $this->renderPagerfanta('twitterBootstrap3View');
    }

    /**
     * @Route("/pagerfanta/view-with-options", name="pagerfanta_view_with_options")
     */
    public function viewWithOptionsAction()
    {
        return $this->renderPagerfanta('viewWithOptions');
    }

    /**
     * @Route("/pagerfanta/view-without-first-page-param", name="pagerfanta_view_without_first_page_param")
     */
    public function viewWithoutFirstPageParamAction(Request $request)
    {
        return $this->defaultWithRequestAction($request, 'viewWithoutFirstPageParam');
    }

    /**
     * @Route("/pagerfanta/default-translated-view", name="pagerfanta_default_translated_view", defaults={"_locale": "es"})
     */
    public function defaultTranslatedViewAction()
    {
        return $this->renderPagerfanta('defaultTranslatedView');
    }

    /**
     * @Route("/pagerfanta/twitter-bootstrap-translated-view", name="pagerfanta_twitter_bootstrap_translated_view", defaults={"_locale": "es"})
     */
    public function twitterBootstrapTranslatedViewAction()
    {
        return $this->renderPagerfanta('twitterBootstrapTranslatedView');
    }

    /**
     * @Route("/pagerfanta/twitter-bootstrap3-translated-view", name="pagerfanta_twitter_bootstrap3_translated_view", defaults={"_locale": "es"})
     */
    public function twitterBootstrap3TranslatedViewAction()
    {
        return $this->renderPagerfanta('twitterBootstrap3TranslatedView');
    }

    /**
     * @Route("/pagerfanta/my-view-1", name="pagerfanta_my_view_1")
     */
    public function myView1Action()
    {
        return $this->renderPagerfanta('myView1');
    }

    /**
     * @Route("/pagerfanta/view-with-route-params", name="pagerfanta_view_with_route_params")
     */
    public function viewWithRouteParamsAction($test = null)
    {
        return $this->renderPagerfanta('viewWithRouteParams');
    }

    /**
     * @Route("/pagerfanta/custom-page", name="pagerfanta_correct_view")
     */
    public function defaultWithRequestAction(Request $request, $name = 'defaultView')
    {
        $template = $this->buildTemplateName($name);
        $pagerfanta = $this->createPagerfanta();
        $pagerfanta->setMaxPerPage($request->query->get('maxPerPage', 10));
        $pagerfanta->setCurrentPage($request->query->get('currentPage', 1));

        return $this->render($template, array(
            'pagerfanta' => $pagerfanta,
        ));
    }

    private function renderPagerfanta($name)
    {
        $template = $this->buildTemplateName($name);
        $pagerfanta = $this->createPagerfanta();

        return $this->render($template, array(
            'pagerfanta' => $pagerfanta
        ));

    }

    private function buildTemplateName($name)
    {
        return sprintf('@BabDevPagerfantaTest/Pagerfanta/%s.html.twig', $name);
    }

    private function createPagerfanta()
    {
        $adapter = $this->createAdapter();

        return new Pagerfanta($adapter);
    }

    private function createAdapter()
    {
        $nbResults = 100;
        $results = range(1, $nbResults);

        return new FixedAdapter($nbResults, $results);
    }
}
