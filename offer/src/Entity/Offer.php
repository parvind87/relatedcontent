<?php
/**
 * @file
 * Contains \Drupal\offer\Entity\Offer.
 */
namespace Drupal\offer\Entity;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
/**
 * Defines the offer entity.
 *
 * @ingroup offer
 *
 * @ContentEntityType(
 * id = "offer",
 * label = @Translation("Offer"),
 * base_table = "offer",
 * data_table = "offer_field_data",
 * revision_table = "offer_revision",
 * revision_data_table = "offer_field_revision",
 * entity_keys = {
 * "id" = "id",
 * "uuid" = "uuid",
 * "label" = "title",
 * "revision" = "vid",
 * "status" = "status",
 * "published" = "status",
 * "uid" = "uid",
 * "owner" = "uid",
 * },
 * revision_metadata_keys = {
 * "revision_user" = "revision_uid",
 * "revision_created" = "revision_timestamp",
 * "revision_log_message" = "revision_log"
 * },
 * handlers = {
 *  "access" = "Drupal\offer\OfferAccessControlHandler",
 *  "views_data" = "Drupal\offer\OfferViewsData",
 *  "form" = {
 *  "add" = "Drupal\offer\Form\OfferForm",
 *  "edit" = "Drupal\offer\Form\OfferForm",
 *  "delete" = "Drupal\offer\Form\OfferDeleteForm",
 *  },
 *  },
 *  links = {
 *   "canonical" = "/offers/{offer}",
 *   "delete-form" = "/offer/{offer}/delete",
 *   "edit-form" = "/offer/{offer}/edit",
 *   "create" = "/offer/create",
 *   },
 * )
 */
class Offer extends EditorialContentEntityBase {
    public static function baseFieldDefinitions(EntityTypeInterface
                                                $entity_type) {
        $fields = parent::baseFieldDefinitions($entity_type); // provides id and uuid fields
$fields['user_id'] = BaseFieldDefinition::create('entity_reference')
    ->setLabel(t('User'))
    ->setDescription(t('The user that created the offer.'))
    ->setSetting('target_type', 'user')
    ->setSetting('handler', 'default')
    ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
    ])
    ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
            'match_operator' => 'CONTAINS',
            'size' => '60',
            'autocomplete_type' => 'tags',
            'placeholder' => '',
        ],
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE);
$fields['title'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Title'))
    ->setDescription(t('The title of the offer'))
    ->setSettings([
        'max_length' => 150,
        'text_processing' => 0,
    ])
    ->setDefaultValue('')
    ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
    ])
    ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE);
$fields['message'] = BaseFieldDefinition::create('string_long')
    ->setLabel(t('Message'))
    ->setRequired(TRUE)
    ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 4,
        'settings' => [
            'rows' => 12,
        ],
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => 0,
        'label' => 'above',
    ])
    ->setDisplayConfigurable('view', TRUE);
$fields['status'] = BaseFieldDefinition::create('boolean')
    ->setLabel(t('Publishing status'))
    ->setDescription(t('A boolean indicating whether the Offer entity
is published.'))
    ->setDefaultValue(TRUE);
$fields['created'] = BaseFieldDefinition::create('created')
    ->setLabel(t('Created'))
    ->setDescription(t('The time that the entity was created.'));
$fields['changed'] = BaseFieldDefinition::create('changed')
    ->setLabel(t('Changed'))
    ->setDescription(t('The time that the entity was last edited.'));
return $fields;
}

/**
 * {@inheritdoc}
 *
 * Makes the current user the owner of the entity
 */
public static function preCreate(EntityStorageInterface
                                 $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
        'user_id' => \Drupal::currentUser()->id(),
    );
}
/**
 * {@inheritdoc}
 */
public function getOwner() {
    return $this->get('user_id')->entity;
}
/**
 * {@inheritdoc}
 */
public function getOwnerId() {
    return $this->get('user_id')->target_id;
}
}