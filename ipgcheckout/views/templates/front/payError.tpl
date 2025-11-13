{* modules/mymodule/views/templates/front/my_template.tpl *}

{extends file='page.tpl'}

{block name='page_header_container'}
  <header class="page-header">
    <h1>{$title}</h1>
  </header>
{/block}

{block name='page_content'}
  <section class="page-content">
    <p>{$text}</p>
  </section>
{/block}