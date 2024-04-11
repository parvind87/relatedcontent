<?php
namespace Drupal\offer;
use Drupal\views\EntityViewsData;
/**
 * Provides views data for Offer entities.
 *
 */
class OfferViewsData extends EntityViewsData {
    /**
     * Returns the Views data for the entity.
     */
    public function getViewsData() {
        $data = parent::getViewsData();
        return $data;
    }
}