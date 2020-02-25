import Model from 'flarum/Model';

export default class ModeratorNote extends Model {
  id = Model.attribute('id');
  note = Model.attribute('note');
  createdAt = Model.attribute('createdAt');
  addedByUser = Model.attribute('addedByUser');
  displayName = Model.attribute('addedByUser.username');
  avatarUrl = Model.attribute('addedByUser.avatarUrl');
  color = Model.attribute('addedByUser.color');
}
