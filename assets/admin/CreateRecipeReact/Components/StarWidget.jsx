import React from "react";

export default function ({ inputs, handleChange }) {
    return (
        <div className="star-widget mx-auto">

            <input checked={inputs["5"]} onChange={handleChange} type="radio" name="rate" id="rate-5"/>
            <label htmlFor="rate-5" className="bi bi-star-fill"></label>

            <input checked={inputs["4"]} onChange={handleChange} type="radio" name="rate" id="rate-4"/>
            <label htmlFor="rate-4" className="bi bi-star-fill"></label>

            <input checked={inputs["3"]} onChange={handleChange} type="radio" name="rate" id="rate-3"/>
            <label htmlFor="rate-3" className="bi bi-star-fill"></label>

            <input checked={inputs["2"]} onChange={handleChange} type="radio" name="rate" id="rate-2"/>
            <label htmlFor="rate-2" className="bi bi-star-fill"></label>

            <input checked={inputs["1"]} onChange={handleChange} type="radio" name="rate" id="rate-1"/>
            <label htmlFor="rate-1" className="bi bi-star-fill"></label>

        </div>
    )
}