import { Injectable } from '@angular/core';
import { Actions, Effect, ofType } from '@ngrx/effects';
import { Observable, defer, of } from 'rxjs';
import { tap, distinctUntilChanged, mergeMap, map } from 'rxjs/operators';
import { SendAction, ReceiveAction, MessagesActionTypes } from '../actions/messages.actions';

import { MessagesService } from '../../services/messages.service';

@Injectable()
export class MessagesEffects {

  @Effect()
  sendActions$: Observable<any> = this.actions.pipe(
    ofType<SendAction>(MessagesActionTypes.SEND),
    mergeMap(action =>
      this.messagesService.getMessages(action.payload).pipe(
        map((data) => new ReceiveAction(data)),
      )
    )
  );

  constructor(
    private actions: Actions,
    private messagesService: MessagesService
  ) {}
}
