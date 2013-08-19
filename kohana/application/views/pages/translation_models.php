<?php 
/*******************************************************************
PHAST Software
Copyright (c)  2013, University of Washington. All rights reserved.

Author: Adrian Laurenzi

PHAST Software
Copyright (c)  2013, University of Washington. All rights reserved.

User, through installation and use of the Phast software (the 
"Software"), hereby accepts, a restricted, non-exclusive, 
non-transferable license to use the Software for academic, research, 
and internal business purposes only, and not for commercial use. 
Commercial use of the Software requires a separately executed written 
license agreement. Please contact phast-project@uw.edu if you are 
interested in a commercial license.

Permission is granted, free of charge, to any person to use the 
Software, and to copy, modify, merge, and publish it provided that 
all copies or modifications of the source code retain the following 
copyright notice and a copy of this License. 

PHAST Software
Copyright (c)  2013, University of Washington. All rights reserved.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*******************************************************************/
?>

<p><a href="<?= Url::base(TRUE) ?>admin/add_translation_model" class="btn">Add Translation Model</a></p>

<? if(count($translation_models) == 0) { ?>
No models to display.
<? } else { ?>
<table class="table table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Category Code</th>
        <th>Target Language</th>
        <th>Date Added</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
<? $i = 1;
foreach($translation_models as $translation_model) { ?>
    <tr>
        <td><?= $i ?></td>
        <td><?= $translation_model['descriptive_name'] ?></td>
        <td><?= $translation_model['category_code'] ?></td>
        <td><?= $translation_model['lang_name'] ?></td>
        <td><?= date(Kohana::config('mainconf.date_format'), $translation_model['date_added']) ?></td>
        <td>
        <?= ($translation_model['model_id'] == $default_model_id) ? '(default)' : '<a href="'.Url::base(TRUE).'admin/set_default_translation_model/'.$translation_model['model_id'].'" class="btn btn-mini">Set as default</a>' ?>
        </td>
    </tr>
<? $i++;
} ?>
    </tbody>
</table>

<a href="<?= Url::base(TRUE) ?>admin/add_translation_model" class="btn">Add Translation Model</a>
<? } ?>
