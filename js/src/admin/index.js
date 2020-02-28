import app from 'flarum/app';
import { extend } from 'flarum/extend';
import PermissionGrid from 'flarum/components/PermissionGrid';

app.initializers.add('fof/moderator-notes', () => {
  extend(PermissionGrid.prototype, 'moderateItems', items => {
    items.add('moderator-notes-view', {
      icon: 'fas fa-images',
      label: app.translator.trans('fof-moderator-notes.admin.permissions.viewnotes'),
      permission: 'user.viewModeratorNotes'
    }, 1),
    
    items.add('moderator-notes-create', {
      icon: 'fas fa-edit',
      label: app.translator.trans('fof-moderator-notes.admin.permissions.createnotes'),
      permission: 'user.createModeratorNotes'
    }, 1);
  });
});
