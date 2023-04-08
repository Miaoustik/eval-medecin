import React, {useCallback, useEffect, useState} from "react";

export default function () {

    const [checked, setChecked] = useState(false)
    const [inputs, setInputs] = useState({
        1: false,
        2: false,
        3: false,
        4: false,
        5: false,
    })


    const handleChange = useCallback((e) => {

        setInputs(prevState => {
            const newState = {...prevState}
            const number = e.target.id.slice(-1)
            Object.keys(newState).forEach(k => {
                if (k == number) {
                    newState[k] = e.target.checked
                } else {
                    newState[k] = false
                }
            })


            return newState
        })

    }, [])

    useEffect(() => {
        let checked = false
        Object.keys(inputs).forEach(e => {
            if (inputs[e] === true) {
                checked = true
            }
        })
        setChecked(checked)
    }, [
        inputs["1"],
        inputs["2"],
        inputs["3"],
        inputs["4"],
        inputs["5"],
    ])

    return (
        <>
            <div className="inputStyle rounded shadow1">
                <p className="text-center text-secondary mb-1 pt-2">La recette est terminée.</p>
                <p className="text-center textNoto avisInterresse mb-1">Votre avis m'intérresse</p>
                <form className=" pb-2">
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
                    <div className={"comment mx-auto " + (checked ? ' show' : '')}>
                        <textarea className="form-control mt-2" rows="3" placeholder="Ecrivez votre avis..."></textarea>
                        <button className="btn btn-primary text-white w-100 mt-2">Envoyer</button>
                    </div>
                </form>
            </div>
        </>



    )
}