import React from 'react';
import ReactDOM from 'react-dom/client';
import Appp from "./Appp";

const reactForm = document.getElementById('reactForm')
const index = ReactDOM.createRoot(reactForm)

index.render (
    <>
        <Appp {...(reactForm.dataset)} />
    </>
)