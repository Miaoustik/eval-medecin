import React, {useCallback, useEffect, useState} from "react";
import useFetch from "../admin/CreateRecipeReact/Hooks/useFetch";
import useStarWidget from "../admin/CreateRecipeReact/Hooks/useStarWidget";
import StarWidget from "../admin/CreateRecipeReact/Components/StarWidget";
import useSignalController from "../admin/CreateRecipeReact/Hooks/useSignalController";

export default function ({recipeid, connected, a}) {

    const controllerRef = useSignalController()

    const { data, loading, setRefresh } = useFetch('/api/recette/' + recipeid + '/avis', controllerRef)
    const { inputs, checked, handleChange } = useStarWidget()
    const [ area, setArea ] = useState('')
    const [ noticed, setNoticed ] = useState(false)



    const handleArea = useCallback((e) => {
        setArea(e.target.value)
    }, [])

    const handleSubmit = useCallback((e) => {
        e.preventDefault()
        const note = Object.entries(inputs).filter((e) => {
            return e[1]
        })[0][0]

        const noticeObj = {
            recipeid,
            note,
            content: area
        }

        const fetchOptions = {
            headers: {
                'Content-Type': 'application/json'
            },
            signal: controllerRef.current.signal,
            body: JSON.stringify(noticeObj),
            method: 'POST'
        }

        fetch('/api/avis/creer', fetchOptions)
            .then(() => {
                setRefresh(prevState => {
                    return !prevState
                })
                setNoticed(true)
            })
            .catch(error =>  console.warn(error))


    }, [
        inputs['1'],
        inputs['2'],
        inputs['3'],
        inputs['4'],
        inputs['5'],
        area
    ])


    if (loading) {
        return (
            <div className="spinner-border" role="status">
                <span className="visually-hidden">Loading...</span>
            </div>
        )
    }



    return (
        <>
            {(connected === '1' && a === '0' &&
                (noticed || data[1]
                    ? (
                        <div className={'alert alert-success'}>
                            Merci pour votre avis.
                        </div>
                        )
                    : (
                        <div className="inputStyle rounded shadow1">
                            <p className="text-center text-secondary mb-1 pt-2">La recette est terminée.</p>
                            {data[1]}
                            <p className="text-center textNoto avisInterresse mb-1">Votre avis m'intérresse</p>
                            <form className=" pb-2" onSubmit={handleSubmit}>
                                <StarWidget inputs={inputs} handleChange={handleChange} />
                                <div className={"comment mx-auto " + (checked ? ' show' : '')}>
                                    <textarea value={area} onChange={handleArea} className="form-control mt-2" rows="3" placeholder="Ecrivez votre avis..."></textarea>
                                    <button className="btn btn-primary text-white w-100 mt-2">Envoyer</button>
                                </div>
                            </form>
                        </div>
                    )
                )
            )}

            <h3 className={'mt-4 secondTitle'}>Commentaires : {data[0].length}</h3>

            { data[0].map((notice, index) => {
                    return (
                        <div key={'notice' + index} className={'card px-4 py-3 mb-4 shadow1'}>
                            <div className={'mb-2'}>
                                <i className={(notice.note >= 1 ? 'starFill' : 'star') + ' bi bi-star-fill'}></i>
                                <i className={(notice.note >= 2 ? 'starFill' : 'star') + ' bi bi-star-fill'}></i>
                                <i className={(notice.note >= 3 ? 'starFill' : 'star') + ' bi bi-star-fill'}></i>
                                <i className={(notice.note >= 4 ? 'starFill' : 'star') + ' bi bi-star-fill'}></i>
                                <i className={(notice.note == 5 ? 'starFill' : 'star') + ' bi bi-star-fill'}></i>
                            </div>
                            <p>{notice.content}</p>
                        </div>
                    )
                })
            }
        </>
    )
}