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
use Drupal\Core\Url;

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
      'image_style'               => '',
      'image_link'                      => '',
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

  	// get image_style and image_link form elements from parent method.
    $element = parent::settingsForm($form, $form_state);

    $link_types = array(
      'content' => t('Content'),
      'file' => t('File'),
    );
    $captions = array(
      'title'   => t('Title text'),
      'alt'     => t('Alt text'),
    );
    $element['slideshow_caption'] = array(
      '#title'          => t('Caption'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('slideshow_caption'),
      '#empty_option'   => t('Nothing'),
      '#options'        => $captions,
    );
    $element['slideshow_caption_link'] = array(
      '#title'          => t('Caption link'),
      '#type'           => 'select',
      '#default_value'  => $this->getSetting('slideshow_caption_link'),
      '#empty_option'   => t('Nothing'),
      '#options'        => $link_types,
      '#states' => array(
        'invisible' => array(
          ':input[name$="[settings_edit_form][settings][slideshow_caption]"]' => array('value' => ''),
        ),
      ),
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
    $element['slideshow_controls_pause'] = array(
      '#title'          => t('Create play/pause button'),
      '#type'           => 'checkbox',
      '#default_value'  => $this->getSetting('slideshow_controls_pause'),
      '#states' => array(
        'visible' => array(
          ':input[name$="[settings_edit_form][settings][slideshow_controls]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $element['slideshow_controls_position'] = array(
      '#title'          => t('Prev/next controls position'),
      '#type'           => 'select',
      '#options'        => array('before' => 'Before', 'after' => 'After'),
      '#default_value'  => $this->getSetting('slideshow_controls_position'),
      '#states' => array(
        'visible' => array(
          ':input[name$="[settings_edit_form][settings][slideshow_controls]"]' => array('checked' => TRUE),
        ),
      ),
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

    // get summary of image_style and image_link from parent method.
    $summary = parent::settingsSummary();

    $image_styles = image_style_options(FALSE);
    // Unset possible 'No defined styles' option.
    unset($image_styles['']);
    // Styles could be lost because of enabled/disabled modules that defines
    // their styles in code.
    $image_style_setting = $this->getSetting('image_style');

    $caption_types = array(
      'title' => t('Title text'),
      'alt'   => t('Alt text'),
    );
    // Display this setting only if there's a caption.
    $caption_types_settings = $this->getSetting('slideshow_caption');
    if (isset($caption_types[$caption_types_settings])) {
      $caption_message = t('Caption: @caption', array('@caption' => $caption_types[$caption_types_settings]));
      $link_types_settings = $this->getSetting('slideshow_caption_link');
      if (isset($link_types[$link_types_settings])) $caption_message .= ' (' . t('Link to: @link', array('@link' => $link_types[$link_types_settings])) . ')';
      $summary[] = $caption_message;
    }

    $summary[] = t('Transition effect: @effect', array('@effect' => $this->getSetting('slideshow_fx')));
    $summary[] = t('Speed: @speed', array('@speed' => $this->getSetting('slideshow_speed')));
    $summary[] = t('Timeout: @timeout', array('@timeout' => $this->getSetting('slideshow_timeout')));

    $orders = array(
      'reverse' => t('Reverse order'),
      'random'  => t('Random order'),
    );
    $orders_settings = $this->getSetting('slideshow_order');
    if (isset($orders[$orders_settings])) {
      $summary[] = $orders[$orders_settings];
    }
    $pause_button_text = "";
    $slideshow_controls_pause = $this->getSetting('slideshow_controls_pause');
    $slideshow_controls = $this->getSetting('slideshow_controls');
    $slideshow_pause = $this->getSetting('slideshow_pause');
    $slideshow_start_on_hover = $this->getSetting('slideshow_start_on_hover');
    if (isset($slideshow_controls_pause) && $slideshow_controls_pause ) $pause_button_text = " " . t("(with play/pause)");
    if (isset($slideshow_controls) && $slideshow_controls) $summary[] = t('Create prev/next controls') . $pause_button_text;
    if (isset($slideshow_pause) && $slideshow_pause) $summary[] = t('Pause on hover');
    if (isset($slideshow_start_on_hover) && $slideshow_start_on_hover) $summary[] = t('Activate on hover');

    switch ($this->getSetting('slideshow_pager')) {
      case 'number':
        $summary[] = t('Pager') . ': ' . t('Slide number');
      break;
      case 'image':
        $pager_image_message = t('Pager') . ': ' . t('Image') . ' (';
        if (isset($image_styles[$this->getSetting('slideshow_pager_image_style')])) {
          $pager_image_message .= t('Image style: @style', array('@style' => $image_styles[$this->getSetting('slideshow_pager_image_style')]));
        }
        else {
          $pager_image_message .= t('Original image');
        }
        $pager_image_message .= ')';
        $summary[] = $pager_image_message;
      break;
    }

    return $summary;
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    // get image html from parent method.
    $images = parent::viewElements($items, $langcode);
    
    static $slideshow_count;
    $slideshow_count = (is_int($slideshow_count)) ? $slideshow_count + 1 : 1;
    $files = $this->getEntitiesToView($items, $langcode);

    // Check plugins
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      if (!file_exists(DRUPAL_ROOT . '/libraries/jquery.cycle/jquery.cycle.all.min.js') && !file_exists(DRUPAL_ROOT . '/libraries/jquery.cycle/jquery.cycle.all.js')) {
        $url = Url::fromRoute('system.status');
        drupal_set_message(t('JQuery Cycle must be installed in order to run the slideshow. Please go to !page for instructions.', array('!page' => \Drupal::l(t('Status Report'), $url))), 'warning', FALSE);
      }
    }
    else {
      $url = Url::fromUri('http://drupal.org/project/libraries');
      drupal_set_message(t('Please install the !module module in order to use Field Slideshow.', array('!module' => \Drupal::l('Libraries API', $url))), 'warning', FALSE);
    }

    $elements = array();
    $entity = array();

    // Get correct caption
    $item_settings = array();
    if ($this->getSetting('slideshow_caption') != '') {
      foreach ($items as $delta => $item) {
        $caption_settings = $this->getSetting('slideshow_caption');
        if ($caption_settings == 'title') {
          $item_settings[$delta]['caption'] = $item->getValue()['title'];
        } elseif ($caption_settings == 'alt') {
          $item_settings[$delta]['caption'] = $item->getValue()['alt'];
        }
      }
    }

    $links = array(
      'image_link'          => 'path',
      'slideshow_caption_link'  => 'caption_path',
    );

    // Loop through required links (because image and caption can have different links).
    foreach ($links as $setting => $path) {
      // Check if the formatter involves a link.
      $link_type = '';
      switch ($this->getSetting($setting)) {
        case 'content':
          $link_type = 'content';
        break;
        case 'file':
          $link_type = 'file';
        case 'colorbox':
          $link_type = 'file';
        break;
      }
      foreach ($items as $delta => $item) {
        $uri = array();
        switch ($link_type) {
          case 'content':
            $entity = $item->getEntity();
            if (!$entity->isNew()) {
              $uri = $entity->urlInfo();
            }
          break;
          case 'file':
            foreach ($files as $file_delta => $file) {
              if (isset($link_type)) {
                $image_uri = $file->getFileUri();
                $uri = Url::fromUri(file_create_url($image_uri));
              }
              $item_settings[$file_delta][$path] = !empty($uri) ? $uri : '';
            }
        }
        $item_settings[$delta][$path] = !empty($uri) ? $uri : '';
      }
    }

    $pager = array(
      '#theme'                => 'field_slideshow_pager',
      '#items'                => $items,
      '#item_settings'        => $item_settings,
      '#pager'                => $this->getSetting('slideshow_pager'),
      '#pager_image_style'    => $this->getSetting('slideshow_pager_image_style'),
      //'#carousel_image_style' => $this->getSetting('slideshow_carousel_image_style'),
      '#slideshow_id'         => $slideshow_count,
      //'#carousel_skin'        => $this->getSetting('slideshow_carousel_skin'),
    );
    $controls = array(
      '#theme'                => 'field_slideshow_controls',
      '#slideshow_id'         => $slideshow_count,
      '#controls_pause'       => $this->getSetting('slideshow_controls_pause'),
    );   
    
    $elements[] = array(
      '#theme'                => 'field_slideshow',
      '#items'                => $items,
      '#item_settings'        => $item_settings,
      '#image_style'          => $this->getSetting('image_style'),
      '#image'                => $images,
      '#order'                => $this->getSetting('slideshow_order'),
      '#controls'             => ($this->getSetting('slideshow_controls') == 1 ? $controls : array()),
      '#controls_position'    => $this->getSetting('slideshow_controls_position'),
      '#pager'                => $this->getSetting('slideshow_pager') !== '' ? $pager : array(),
      '#pager_position'       => $this->getSetting('slideshow_pager_position'),
      '#entity'               => $entity,
      '#slideshow_id'         => $slideshow_count,
      '#js_variables'         => array(
        'fx'                   => $this->getSetting('slideshow_fx'),
        'speed'                => $this->getSetting('slideshow_speed'),
        'timeout'              => $this->getSetting('slideshow_timeout'),
        'pause'                => $this->getSetting('slideshow_pause'),
        'start_on_hover'       => $this->getSetting('slideshow_start_on_hover'),
        // 'carousel_visible'     => $this->getSetting('slideshow_carousel_visible'),
        // 'carousel_scroll'      => $this->getSetting('slideshow_carousel_scroll'),
        // 'carousel_speed'       => $this->getSetting('slideshow_carousel_speed'),
        // 'carousel_vertical'    => $this->getSetting('slideshow_carousel_vertical'),
        // 'carousel_circular'    => $this->getSetting('slideshow_carousel_circular'),
        // 'carousel_follow'      => $this->getSetting('slideshow_carousel_follow'),
        // 'carousel_skin'        => $this->getSetting('slideshow_carousel_skin'),
        // Need to access the following variables in js too
        'pager'                => $this->getSetting('slideshow_pager'),
        'controls'             => $this->getSetting('slideshow_controls') === 1 ? $controls : array(),
      ),
    );
    return $elements;
  }
}