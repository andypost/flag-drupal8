<?php
/**
 * @file
 * Contains the AJAXLinkController class.
 */

namespace Drupal\flag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\flag\FlagInterface;
use Drupal\flag\FlaggingInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

class AJAXLinkController extends ControllerBase {

  /**
   * Performs a flagging when called via a route.
   *
   * This method is invoked when a user clicks an AJAX flagging link provided
   * by the AJAXactionLink plugin.
   *
   * @param $flag_id
   * @param $entity_id
   * @return AjaxResponse
   * @see \Drupal\flag\Plugin\ActionLink\AJAXactionLink
   */
  public function flag($flag_id, $entity_id) {
    $flagging = \Drupal::service('flag')->flag($flag_id, $entity_id);

    $flag = $flagging->getFlag();
    $entity = $flagging->getFlaggable();

    return $this->generateResponse('unflag', $flag, $entity);
  }

  /**
   * Performs an unflagging when called via a route.
   *
   * This method is invoked when a user clicks an AJAX unflagging link provided
   * by the AJAXactionLink plugin.
   *
   * @param $flag_id
   * @param $entity_id
   * @return AjaxResponse
   *
   * @see \Drupal\flag\Plugin\ActionLink\AJAXactionLink
   */
  public function unflag($flag_id, $entity_id) {
    $flagService = \Drupal::service('flag');
    $flagService->unflag($flag_id, $entity_id);

    $flag = $flagService->getFlagById($flag_id);
    $entity = $flagService->getFlaggableById($flag, $entity_id);

    return $this->generateResponse('flag', $flag, $entity);
  }

  /**
   * @param $action
   * @param FlagInterface $flag
   * @param EntityInterface $entity
   * @return AjaxResponse
   */
  protected function generateResponse($action, FlagInterface $flag, EntityInterface $entity) {
    // Create a new AJAX response.
    $response = new AjaxResponse();

    // Get the link type plugin.
    $linkType = $flag->getLinkTypePlugin();

    // Generate the link render array and get the link CSS ID.
    $link = $linkType->renderLink($action, $flag, $entity);
    $linkId = '#' . $link['#attributes']['id'];

    // Create a new JQuery Replace command to update the link display.
    $replace = new ReplaceCommand($linkId, drupal_render($link));
    $response->addCommand($replace);

    return $response;
  }

} 