import app from 'flarum/app';

app.initializers.add('fof-moderator-notes', () => {
    app.extensionData
        .for('fof-moderator-notes')
        .registerPermission(
            {
                icon: 'fas fa-images',
                label: app.translator.trans('fof-moderator-notes.admin.permissions.viewnotes'),
                permission: 'user.viewModeratorNotes',
            },
            'moderate'
        )
        .registerPermission(
            {
                icon: 'fas fa-edit',
                label: app.translator.trans('fof-moderator-notes.admin.permissions.createnotes'),
                permission: 'user.createModeratorNotes',
            },
            'moderate'
        )
        .registerPermission(
            {
                icon: 'far fa-trash-alt',
                label: app.translator.trans('fof-moderator-notes.admin.permissions.deletenotes'),
                permission: 'user.deleteModeratorNotes',
            },
            'moderate'
        );
});
