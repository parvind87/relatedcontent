<?php

namespace Drupal\related_content;

use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Database\Connection;

/**
 * Defines a custom service for populating related content.
 *
 * This service provides methods to fetch related content based on current Article
 * Class RelatedContentService
 *
 */
class RelatedContentService
{

    /**
     * The Database connection.
     *
     * @var \Drupal\Core\Database\Connection
     */
    private Connection $database;

    /**
     * The access manager service.
     *
     * @var \Drupal\Core\Routing\CurrentRouteMatch
     */
    protected CurrentRouteMatch $routeMatch;

  /**
   * Constructs a new RelatedContentService object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection service.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $route_match
   *   The current route match service.
   */
    public function __construct(Connection $database, CurrentRouteMatch $route_match)
    {
        $this->database = $database;
        $this->routeMatch = $route_match;
    }


  /**
   * Retrieves an array of related content items based on the current Article node.
   *
   * @return array
   *   An array of related content items.
   */
    public function getRelatedContents(): array
    {
        $node = $this->routeMatch->getParameter('node');
        if ($this->routeMatch->getRouteName() == 'entity.node.canonical' && $node->getType() == 'article') {
            $current_article_id = $node->id();
            $current_cat_id = $node->get('field_category')->getString();
            $cur_uid = $node->getOwner()->id();
        }
        //Articles in the same category and by the same author as the current article.

        $records = $this->getPriorityArticles($current_article_id, $current_cat_id, $cur_uid);

        if (count($records) < 5) {
            //Articles in the same category but by different authors.
            $prio2 = $this->getPriorityArticles($current_article_id, $current_cat_id);
            $records = $this->prio_merge($records, $prio2);
        }
        if (count($records) < 5) {
            //Articles in different categories but by the same author.
            $prio3 = $this->getPriorityArticles($current_article_id, '', $cur_uid);
            $records = $this->prio_merge($records, $prio3);
        }
        if (count($records) < 5) {
            //Articles in different categories and by different authors.
            $prio4 = $this->getPriorityArticles($current_article_id);
            $records = $this->prio_merge($records, $prio4);
        }
      // Return a slice of the merged records up to 5 items.
        return array_slice($records, 0, 5, true);

    }
  /**
   * Merges two arrays while preserving keys.
   *
   * @param array $prio1
   *   The first array to merge.
   * @param array $prio2
   *   The second array to merge.
   *
   * @return array
   *   The merged array.
   */
    function prio_merge($prio1, $prio2): array
    {
        $prio = [];
        foreach ($prio1 as $key => $item) {
            $prio[$key] = $item;
        }
        foreach ($prio2 as $key => $item) {
            $prio[$key] = $item;
        }
        return $prio;
    }
  /**
   * Retrieves priority articles based on specified criteria.
   *
   * @param int $nid
   *   The node ID.
   * @param string|null $cat_id
   *   The category ID (optional).
   * @param int|null $user_id
   *   The user ID (optional).
   *
   * @return array
   *   An array of priority articles.
   */
    function getPriorityArticles($nid, $cat_id = null, $user_id = null): array {
        $query = $this->database->select('node_field_data', 'n');
        $query->fields('n', ['nid', 'title']);
        $query->fields('nc', ['field_category_target_id']);
        $query->condition('n.type', 'article');
        $query->condition('n.status', 1);
        $query->condition('n.nid', $nid, '!=');
        $query->join('node__field_category', 'nc', 'nc.entity_id = n.nid');
        if (!empty($cat_id)) {
            $query->condition('nc.field_category_target_id', $cat_id);
        }
        if (!empty($user_id)) {
            $query->condition('n.uid', $user_id);
        }
        $query->orderBy('title', 'ASC');
        $query->orderBy('created', 'DESC');
        $results = $query->execute()->fetchAll();

      $all_articles = [];
        foreach ($results as $item) {
          $all_articles[$item->nid] = $item;
        }
      return $all_articles;

    }

}
