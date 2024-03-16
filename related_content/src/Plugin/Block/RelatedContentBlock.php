<?php

namespace Drupal\related_content\Plugin\Block;

use Drupal\related_content\RelatedContentService;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Related Content' block.
 *
 * @Block(
 *   id = "related_content",
 *   admin_label = @Translation("Related content")
 * )
 */
class RelatedContentBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Custom RelatedContent service.
   *
   * @var \Drupal\related_content\RelatedContentService
   */
  protected $relatedcontentservice;

  /**
   * Constructor.
   */
  public function __construct(
    array                 $configuration,
                          $plugin_id,
                          $plugin_definition,
    RelatedContentService $related_content_service
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->relatedcontentservice = $related_content_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('related_content.related_content_service'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $all_results = $this->relatedcontentservice->getRelatedContents();
    //dpm($all_results);
    $renderable = [
      '#theme' => 'related_content',
      '#data' => $all_results,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
    return $renderable;
  }

}
