import {Injectable} from '@angular/core';
import {Observable, Observer, throwError, BehaviorSubject} from 'rxjs';
import {map, catchError} from 'rxjs/operators';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

@Injectable({
  providedIn: 'root'
})

/**
 * Service for connecting socket server and receiving/listening response
 */
export class EchoPusherService {

  private echoPusher!: Echo;
  listnerData = new BehaviorSubject<string>('');
  observer: Observer<void | any[]>;

  constructor() {
    const io = require('pusher-js');

    // configuration to connect with socket server (Its "Soketi" server in backend)
    this.echoPusher = new Echo({
      broadcaster: 'pusher',
      key: 'app-key',
      wsHost: '172.17.0.1',
      wsPort: '8000',
      wssPort: '8000',
      forceTLS: false,
      encrypted: true,
      disableStats: true,
      enabledTransports: ['ws', 'wss'],
      cluster: '',
      authEndpoint: '/verify-token' // should be authentication URL for private channel communication
    });
  }

  /**
   * Start listening 'event' on a channel 'channel'
   * @param channel
   * @param event
   */
  public startListening(channel: string, event: string) {
    // listening on a public channel
    this.echoPusher.channel(channel).listen(event, (response) => {
      // this.listnerData.next(response);
      this.observer.next(response);
    });
    return this.createObservable()
      .pipe(catchError(this.handleError));

    // listening on a private channel (required authentication)
    // this.echoPusher.private(channel).listen(event, (response) => {
    //   return response;
    // });
  }

  createObservable(): Observable<void> {
    return new Observable(observer => {
      this.observer = observer;
    });
  }

  /**
   * Stop listening 'event' on a channel 'channel'
   * @param channel
   * @param event
   */
  public stopListening(channel: string, event: string) {
    // this.echoPusher.private(channel).stopListening(event);
    this.echoPusher.channel(channel).stopListening(event);
  }

  /**
   * Leave a channel 'channel'
   * @param channel
   */
  public leaveChannel(channel: string) {
    this.echoPusher.leaveChannel(channel);
  }

  private handleError(error) {
    if (error.error instanceof ErrorEvent) {
      // A client-side or network error occurred. Handle it accordingly.
      console.error('An error occurred:', error.error.message);
    } else {
      // The backend returned an unsuccessful response code.
      // The response body may contain clues as to what went wrong,
      console.error(
        `Backend returned code ${error.status}, ` +
        `body was: ${error.error}`);
    }
    // return an observable with a user-facing error message
    return throwError(error || 'Socket.io server error');
  };
}
