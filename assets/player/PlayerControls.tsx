import React, { useState } from 'react';
import { Empty, Loaded, PlaybackController, PlaybackEmitter, PlaybackStatus } from './PlaybackTypes';
import { Track } from '../tracks/TrackTypes';
import { Pause, Play } from 'react-feather';

type PlayerProps = {
    emitter: PlaybackEmitter;
    controller: PlaybackController;
    tracks: Track[];
    currentTrack: Track | null;
};

const PlayerControls: React.FC<PlayerProps> = ({ currentTrack, controller, emitter }) => {
    const [status, setStatus] = useState<PlaybackStatus>(new Empty());

    emitter.on('status', (newStatus) => setStatus(newStatus));

    function handlePlayPause() {
        if (status instanceof Empty) {
            return;
        }

        if (status.paused) {
            controller.resume();
        } else {
            controller.pause();
        }
    }

    return (
        <div className="player">
            <div className="player-controls">
                <button type="button" className="player-btn player-btn-play-pause" disabled={currentTrack === null}
                        onClick={handlePlayPause}>
                    {(status instanceof Loaded && !status.paused)
                        ? <Pause/>
                        : <Play/>
                    }
                </button>
            </div>
            {status instanceof Loaded && (
                <div className="player-position">
                    {status.position.toString()} / {status.totalDuration.toString()}
                </div>
            )}
        </div>
    );
};

export default PlayerControls;
