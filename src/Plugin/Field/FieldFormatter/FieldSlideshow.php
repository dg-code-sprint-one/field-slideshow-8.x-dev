<?php

/**
 * @file
 * Contains \Drupal\field_slideshow\Plugin\Field\FieldFormatter\Slideshow.
 */

namespace Drupal\field_slideshow\Plugin\Field\FieldFormatter;

use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use \InvalidArgumentException;

/**
 * Plugin implementation of the 'slideshow' formatter.
 *
 * @FieldFormatter(
 *   id = "slideshow",
 *   label = @Translation("Slideshow"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class FieldSlideshow extends ImageFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'slideshow_image_style'               => '',
      'slideshow_link'                      => '',
      'slideshow_colorbox_image_style'      => '',
      'slideshow_colorbox_slideshow'        => '',
      'slideshow_colorbox_slideshow_speed'  => '4000',
      'slideshow_colorbox_transition'       => 'elastic',
      'slideshow_colorbox_speed'            => '350',
      'slideshow_caption'                   => '',
      'slideshow_caption_link'              => '',
      'slideshow_fx'                        => 'fade',
      'slideshow_speed'                     => '1000',
      'slideshow_timeout'                   => '4000',
      'slideshow_order'                     => '',
      'slideshow_controls'                  => 0,
      'slideshow_controls_pause'            => 0,
      'slideshow_controls_position'         => 'after',
      'slideshow_pause'                     => 0,
      'slideshow_start_on_hover'            => 0,
      'slideshow_pager'                     => '',
      'slideshow_pager_position'            => 'after',
      'slideshow_pager_image_style'         => '',
    ) + parent::defaultSettings();
  }

	/**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
  	$image_styles = image_style_options(FALSE);
    $element['slideshow_image_style'] = array(
      '#title'          => t('Image style'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('slideshow_image_style'),
      '#empty_option'   => t('None (original image)'),
      '#options'        => $image_styles,
    );
    $links = array(
      'content' => t('Content'),
      'file'    => t('File'),
    );
    $element['slideshow_link'] = array(
      '#title'          => t('Link image to'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('slideshow_link'),
      '#empty_option'   => t('Nothing'),
      '#options'        => $links,
    );
    $captions = array(
      'title'   => t('Title text'),
      'alt'     => t('Alt text'),
    );
    $element['slideshow_fx'] = array(
      '#title'          => t('Transition effect'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('slideshow_fx'),
      '#options'        => array(
        'blindX'      => t('blindX'),
        'blindY'      => t('blindY'),
        'blindZ'      => t('blindZ'),
        'cover'       => t('cover'),
        'curtainX'    => t('curtainX'),
        'curtainY'    => t('curtainY'),
        'fade'        => t('fade'),
        'fadeZoom'    => t('fadeZoom'),
        'growX'       => t('growX'),
        'growY'       => t('growY'),
        'scrollUp'    => t('scrollUp'),
        'scrollDown'  => t('scrollDown'),
        'scrollLeft'  => t('scrollLeft'),
        'scrollRight' => t('scrollRight'),
        'scrollHorz'  => t('scrollHorz'),
        'scrollVert'  => t('scrollVert'),
        'shuffle'     => t('shuffle'),
        'slideX'      => t('slideX'),
        'slideY'      => t('slideY'),
        'toss'        => t('toss'),
        'turnUp'      => t('turnUp'),
        'turnDown'    => t('turnDown'),
        'turnLeft'    => t('turnLeft'),
        'turnRight'   => t('turnRight'),
        'uncover'     => t('uncover'),
        'wipe'        => t('wipe'),
        'zoom'        => t('zoom'),
      ),
    );
    $element['slideshow_speed'] = array(
      '#title'          => t('Transition speed'),
      '#type'           => 'textfield',
      '#size'           => 5,
      '#default_value'  => $this->getSetting('slideshow_speed'),
      '#description'    => t('Duration of transition (ms).'),
      '#required'       => TRUE,
    );
    $element['slideshow_timeout'] = array(
      '#title'          => t('Timeout'),
      '#type'           => 'textfield',
      '#size'           => 5,
      '#default_value'  => $this->getSetting('slideshow_timeout'),
      '#description'    => t('Time between transitions (ms). Enter 0 to disable automatic transitions (then, enable pager and/or controls).'),
      '#required'       => TRUE,
    );
    $element['slideshow_order'] = array(
      '#title'          => t('Order'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('slideshow_order'),
      '#empty_option'   => t('Normal'),
      '#options'        => array(
        'reverse' => t('Reverse'),
        'random'  => t('Random'),
      ),
    );
    $element['slideshow_controls'] = array(
      '#title'          => t('Create prev/next controls'),
      '#type'           => 'checkbox',
      '#default_value'  => $this->getSetting('slideshow_controls'),
    );
    $element['slideshow_pause'] = array(
      '#title'          => t('Pause on hover'),
      '#type'           => 'checkbox',
      '#default_value'  => $this->getSetting('slideshow_pause'),
    );
    $element['slideshow_start_on_hover'] = array(
      '#title'          => t('Activate on hover'),
      '#type'           => 'checkbox',
      '#default_value'  => $this->getSetting('slideshow_start_on_hover'),
    );
    $element['slideshow_pager'] = array(
      '#title'          => t('Pager'),
      '#type'           => 'select',
      '#options'        => array('number' => 'Slide number', 'image' => 'Image'),
      '#empty_option'   => t('None'),
      '#default_value'  => $this->getSetting('slideshow_pager'),
    );
    $element['slideshow_pager_position'] = array(
      '#title'          => t('Pager position'),
      '#type'           => 'select',
      '#options'        => array('before' => 'Before', 'after' => 'After'),
      '#default_value'  => $this->getSetting('slideshow_pager_position'),
      '#states' => array(
        'invisible' => array(
          ':input[name$="[settings_edit_form][settings][slideshow_pager]"]' => array('value' => ''),
        ),
      ),
    );
    $element['slideshow_pager_image_style'] = array(
      '#title'          => t('Pager image style'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('slideshow_pager_image_style'),
      '#empty_option'   => t('None (original image)'),
      '#options'        => image_style_options(FALSE),
      '#states' => array(
        'visible' => array(
          ':input[name$="[settings_edit_form][settings][slideshow_pager]"]' => array('value' => 'image'),
        ),
      ),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    $image_styles = image_style_options(FALSE);
    // Unset possible 'No defined styles' option.
    unset($image_styles['']);
    // Styles could be lost because of enabled/disabled modules that defines
    // their styles in code.
    $image_style_setting = $this->getSetting('image_style');
    if (isset($image_styles[$image_style_setting])) {
      $summary[] = t('Image style: @style', array('@style' => $image_styles[$image_style_setting]));
    }
    else {
      $summary[] = t('Original image');
    }
    $link_types = array(
      'content'   => t('content'),
      'file'      => t('file'),
      'colorbox'  => t('Colorbox'),
    );
    // Display this setting only if image is linked.
    $link_types_settings = $this->getSetting('slideshow_link');
    if (isset($link_types[$link_types_settings])) {
      $link_type_message = t('Link to: @link', array('@link' => $link_types[$link_types_settings]));
      if ($this->getSetting('slideshow_link') == 'colorbox') {
        
      }
      $summary[] = $link_type_message;
    }

    return $summary;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $image_style_setting = $this->getSetting('image_style');

    // Determine if Image style is required.
    $image_style = NULL;
    if (!empty($image_style_setting)) {
      $image_style = entity_load('image_style', $image_style_setting);
    }
    foreach ($items as $delta => $item) {
      if ($item->entity) {
        $image_uri = $item->entity->getFileUri();
        // Get image style URL
        if ($image_style) {
          $image_uri = ImageStyle::load($image_style->getName())->buildUrl($image_uri);
        } else {
          // Get absolute path for original image
          $image_uri = $item->entity->url();
        }
        $elements[$delta] = array(
          '#markup' => $image_uri,
        );
      }
    }
    return $elements;
  }
}