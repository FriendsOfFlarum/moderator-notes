import Extend from 'flarum/common/extenders';
import app from 'flarum/admin/app';

export default [
  new Extend.Admin()
    .permission(
      () => ({
        icon: 'fas fa-sticky-note',
        label: app.translator.trans('fof-moderator-notes.admin.permissions.viewnotes'),
        permission: 'user.viewModeratorNotes',
      }),
      'moderate',
      95
    )
    .permission(
      () => ({
        icon: 'fas fa-edit',
        label: app.translator.trans('fof-moderator-notes.admin.permissions.createnotes'),
        permission: 'user.createModeratorNotes',
      }),
      'moderate',
      95
    )
    .permission(
      () => ({
        icon: 'far fa-trash-alt',
        label: app.translator.trans('fof-moderator-notes.admin.permissions.deletenotes'),
        permission: 'user.deleteModeratorNotes',
      }),
      'moderate',
      95
    ),
];
