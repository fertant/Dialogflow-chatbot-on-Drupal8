import { Component, OnInit } from '@angular/core';
import { FormGroup, FormControl } from '@angular/forms';
import { Store } from '@ngrx/store';
import { Actions, ofType } from '@ngrx/effects';
import { ReceiveAction, MessagesActionTypes } from '../../shared/store/actions/messages.actions';
import { MessageModel } from '../../shared/model/message';

@Component({
  selector: 'app-message-form',
  templateUrl: './message-form.component.html',
  styleUrls: ['./message-form.component.scss']
})
export class MessageFormComponent implements OnInit {
  messageSendForm = new FormGroup({
    message: new FormControl(''),
  });
  sessionId: string;

  constructor(
    private actions: Actions,
    private store: Store<any>
  ) {
    actions.pipe(
      ofType(MessagesActionTypes.RECEIVED)
    ).subscribe((state: ReceiveAction) => {
      this.sessionId = state.payload[0].session;
    });
  }

  ngOnInit(): void {
    this.sessionId = '0';
  }

  onSubmit(event: Event) {
    event.preventDefault();
    this.store.dispatch({
      type: MessagesActionTypes.SEND,
      payload: new MessageModel('you', this.messageSendForm.value.message, this.sessionId)
    });
  }
}
