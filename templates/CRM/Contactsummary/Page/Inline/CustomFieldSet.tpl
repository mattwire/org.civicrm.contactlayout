{assign var='customGroupId' value=$block.custom_group_id}
{assign var='customValues' value=$viewCustomData.$customGroupId}
{foreach from=$customValues item='cd_edit' key='customRecId'}
  {include file="CRM/Contact/Page/View/CustomDataFieldView.tpl" cgcount=1}
{/foreach}
