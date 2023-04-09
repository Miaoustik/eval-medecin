import {useCallback, useEffect, useMemo, useState} from "react";

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
        inputs["5"]
    ])

    return useMemo(() => ({
        checked,
        inputs,
        handleChange
    }), [
        checked,
        inputs['1'],
        inputs['2'],
        inputs['3'],
        inputs['4'],
        inputs['5'],
        handleChange
    ])
}