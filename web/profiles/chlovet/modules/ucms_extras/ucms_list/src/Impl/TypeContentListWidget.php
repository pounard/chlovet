<?php

namespace MakinaCorpus\Ucms\ContentList\Impl;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManager;

use MakinaCorpus\Ucms\ContentList\AbstractContentList;
use MakinaCorpus\Ucms\Contrib\TypeHandler;
use MakinaCorpus\Ucms\Dashboard\Page\PageState;
use MakinaCorpus\Ucms\Site\Site;

/**
 * List content using content type
 */
class TypeContentListWidget extends AbstractContentList
{
    protected $typeHandler;

    public function __construct(EntityManager $entityManager, TypeHandler $typeHandler)
    {
        parent::__construct($entityManager);

        $this->typeHandler = $typeHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(EntityInterface $entity, Site $site, PageState $pageState, $options = [])
    {
          $typeList = $options['type'];

          if (empty($typeList)) {
              return [];
          }

          $exclude    = [];
          $limit      = $pageState->getLimit();
          // @@todo fixme
          $usePager   = false ; //$pageState->

          // Exclude current node whenever it matches the conditions
          if (($current = menu_get_object()) && in_array($current->bundle(), $typeList)) {
              $exclude[] = $current->id();
          }

          $query = db_select('node', 'n');
          $query->join('ucms_site_node', 'un', 'un.nid = n.nid');

          if ($exclude) {
              $query->condition('n.nid', $exclude, 'NOT IN');
          }

          $query
              ->fields('n', ['nid'])
              ->condition('n.type', $typeList)
              ->condition('n.status', NODE_PUBLISHED)
              ->condition('un.site_id', $site->getId())
              ->orderBy('n.' . $pageState->getSortField(), $pageState->getSortOrder())
              ->addMetaData('entity', $entity)
              ->addMetaData('ucms_list', $typeList)
              ->addTag('node_access')
          ;

          // We CANNOT get view_mode of full node with hook_field_formatter_view().
          // That's why we do this ugly check to avoid rewriting all everything...
          // Hope (!) it will do the tricks before YOU apply a real fix ;-)
          if ($usePager) {
              $query = $query->extend('PagerDefault'); // You get a clone. NEEDED!!
              $query->limit($limit);
          } else {
              $query->range(0, $limit);
          }

          return $query->execute()->fetchCol();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return [
            'type' => [],
        ];
    }

    /**
     * Return 'List by types' allowed values.
     */
    protected function getContentTypeList()
    {
        return $this->typeHandler->getTypesAsHumanReadableList($this->typeHandler->getEditorialContentTypes());
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsForm($options = [])
    {
        return [
            'type' => [
                '#type'             => 'select',
                '#title'            => $this->t("Content types to display"),
                '#options'          => $this->getContentTypeList(),
                '#default_value'    => $options['type'],
                '#element_validate' => ['ucms_list_element_validate_filter'],
                '#multiple'         => true,
            ],
        ];
    }
}
