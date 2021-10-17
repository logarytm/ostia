import React from "react";
import { Link, Route, Switch } from "react-router-dom";
import Home from "../pages/Home";
import { Emitter } from "event-kit";
import { PlaybackEmissions } from "../player/PlaybackTypes";
import PlaybackDriver from "../player/PlaybackDriver";
import TrackListPlaybackController from "../player/TrackListPlaybackController";
import Player from "../player/Player";
import Duration, { DurationData } from "../common/Duration";
import { Track } from "../tracks/TrackTypes";

const Category = () => (
    <div>
        <h2>Category</h2>
    </div>
);

const Products = () => (
    <div>
        <h2>Products</h2>
    </div>
);

type TrackData = {
    id: string;
    title: string;
    duration: DurationData;
}

declare var __tracks: TrackData[];

const tracksFromServer: Track[] = __tracks.map((trackData, index) => new Track(
    trackData.id,
    index,
    trackData.title,
    Duration.fromSeconds(trackData.duration.totalSeconds),
));

const emitter = new Emitter<PlaybackEmissions, PlaybackEmissions>();
const driver = new PlaybackDriver(emitter);
const controller = new TrackListPlaybackController(emitter, driver, tracksFromServer);

export default function App() {
    return (
        <div className="wrap">
            <nav className="navbar navbar-light">
                <ul className="nav navbar-nav">
                    <li>
                        <Link to="/">Home</Link>
                    </li>
                    <li>
                        <Link to="/category">Category</Link>
                    </li>
                    <li>
                        <Link to="/products">Products</Link>
                    </li>
                </ul>
            </nav>

            <Switch>
                <Route exact path="/"><Home controller={controller}/></Route>
                <Route path="/category"><Category/></Route>
                <Route path="/products"><Products/></Route>
            </Switch>

            <Player controller={controller}/>
        </div>
    );
}
