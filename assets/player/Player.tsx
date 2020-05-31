import React, { useState } from 'react';
import { Empty, Loaded, PlaybackEmitter, PlaybackStatus } from './PlaybackStatus';
import { Track } from '../tracks/TrackTypes';
import PlaybackDriver from './PlaybackDriver';
import { Pause, Play } from 'react-feather';

type PlayerProps = {
    emitter: PlaybackEmitter;
    driver: PlaybackDriver;
    tracks: Track[];
};

const Player: React.FC<PlayerProps> = ({ driver, emitter }) => {
    const [status, setStatus] = useState<PlaybackStatus>(new Empty());

    emitter.on('status', (newStatus) => setStatus(newStatus));

    function handlePlayPause(e) {
        if (status instanceof Empty) {
            return;
        }

        if (status.paused) {
            driver.resume();
        } else {
            driver.pause();
        }
    }

    return (
        <div className="player">
            <div className="player-controls">
                <button type="button" className="player-btn player-btn-play-pause" disabled={status instanceof Empty}
                        onClick={handlePlayPause}>
                    {(status instanceof Loaded && !status.paused)
                        ? <Pause stroke="white"/>
                        : <Play stroke="white"/>
                    }
                </button>
            </div>
        </div>
    );
};

export default Player;
