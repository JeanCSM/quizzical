<?php defined('APPPATH') or die ('No outside access allowed.');

/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is Quizzical.
 *
 * The Initial Developer of the Original Code is Jonathan Wilde.
 * Portions created by the Initial Developer are Copyright (C) 2010
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */

// First, we need to load up all of Dwoo
require_once Kohana::find_file('vendor', 'dwoo/lib/dwooAutoload');

class Template {
    protected $file = NULL;
    protected $data = NULL;
    
    protected static $dwoo;
    public static $data_global = array();
    
    public static function factory ($file = NULL, array $data = NULL)
    {
        return new Template($file, $data);
    }
    
    public function __construct ($file = NULL, array $data = NULL)
    {
        $this->data = new Dwoo_Data();
        
        if (isset($file))
        {
            $this->set_filename($file);
        }
        
        if (isset($data))
        {
            $this->set($data);
        }
    }
    
    public static function set_global ($key, $data = null)
    {
        if (is_array($key) and ! isset($data))
        {
            Template::$data_global = array_merge(Template::$data_global, $key);
        }
        else if (isset($data))
        {
            Template::$data_global[$key] = $data;
        }
    }
    
    public function set_filename ($file)
    {
        $this->file = new Template_File($file);
    }
    
    public function set ($key, $value = null)
    {
        $this->data->assign($key, $value);
    }
    
    protected static function _render ($file, $data)
    {
        if ( ! isset($dwoo))
        {
            $loader = new Dwoo_Loader(Kohana::$cache_dir);
            $loader->addDirectory(APPPATH . '/classes/template/plugin');
        
            $dwoo = new Template_Engine();
            $dwoo->setCompileDir(Kohana::$cache_dir);
            $dwoo->setLoader($loader);
        }
        
        return $dwoo->get($file, $data);
    }
    
    public function render ()
    {
        $this->set(Template::$data_global);
        return Template::_render($this->file, $this->data);
    }
}
