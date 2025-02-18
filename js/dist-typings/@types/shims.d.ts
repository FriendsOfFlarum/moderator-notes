import User from 'flarum/common/models/User';

declare module 'flarum/common/models/User' {
  export default interface User {
    canViewModeratorNotes(): boolean;
    canCreateModeratorNotes(): boolean;
    canDeleteModeratorNotes(): boolean;
    moderatorNoteCount(): number;
  }
}
