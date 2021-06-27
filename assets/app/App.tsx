import React from "react";
import { Link, Route, Switch } from "react-router-dom";
import Home from "../pages/Home";

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

export default function App() {
    return (
        <>
            <nav className="navbar navbar-light">
                <ul className="nav navbar-nav">
                    <li>
                        <Link to="/app">Home</Link>
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
                <Route exact path="/app"><Home /></Route>
                <Route path="/category"><Category /></Route>
                <Route path="/products"><Products /></Route>
            </Switch>
        </>
    );
}
