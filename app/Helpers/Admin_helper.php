<?php

if (!function_exists('admin_dashboard')) {
  function admin_dashboard() {
    return base_url('admin/dashboard');
  }
}

if (!function_exists('admin_source_link')) {
  function admin_source_link($source = null, $suffix = null)
  {
    $link = base_url('admin/sources');
    if ($source) {
      $link .= '/' . $source['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}

if (!function_exists('admin_user_link')) {
  function admin_user_link($user = null, $suffix = null)
  {
    $link = base_url('admin/users');
    if ($user) {
      $link .= '/' . $user['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}

if (!function_exists('admin_language_link')) {
  function admin_language_link($language = null, $suffix = null)
  {
    $link = base_url('admin/languages');
    if ($language) {
      $link .= '/' . $language['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}

if (!function_exists('admin_location_link')) {
  function admin_location_link($location = null, $suffix = null)
  {
    $link = base_url('admin/locations');
    if ($location) {
      $link .= '/' . $location['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}

if (!function_exists('admin_location_source_link')) {
  function admin_location_source_link($location, $location_source = null, $suffix = null)
  {
    $link = admin_location_link($location) . '/sources';
    if ($location_source) {
      $link .= '/' . $location_source['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}

if (!function_exists('admin_location_trans_link')) {
  function admin_location_trans_link($location, $location_trans = null, $suffix = null)
  {
    $link = admin_location_link($location) . '/trans';
    if ($location_trans) {
      $link .= '/' . $location_trans['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}

if (!function_exists('admin_category_link')) {
  function admin_category_link($category = null, $suffix = null)
  {
    $link = base_url('admin/categories');
    if ($category) {
      $link .= '/' . $category['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}

if (!function_exists('admin_category_trans_link')) {
  function admin_category_trans_link($category, $category_trans = null, $suffix = null)
  {
    $link = admin_category_link($category) . '/trans';
    if ($category_trans) {
      $link .= '/' . $category_trans['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}

if (!function_exists('admin_item_link')) {
  function admin_item_link($item = null, $suffix = null)
  {
    $link = base_url('admin/items');
    if ($item) {
      $link .= '/' . $item['id'];
    }

    if ($suffix) {
      $link .= '/' . $suffix;
    }

    return $link;
  }
}
